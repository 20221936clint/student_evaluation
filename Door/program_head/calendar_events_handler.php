<?php
require_once '../../data/session_security.php';
check_auth('program_head', '../login.php');
require_once '../../data/config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'get_events':
        getEvents($pdo);
        break;
    case 'get_event':
        getEvent($pdo);
        break;
    case 'create_event':
        createEvent($pdo);
        break;
    case 'update_event':
        updateEvent($pdo);
        break;
    case 'delete_event':
        deleteEvent($pdo);
        break;
    case 'get_instructors':
        getInstructors($pdo);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function getEvents($pdo) {
    try {
        $month = intval($_GET['month'] ?? date('m'));
        $year = intval($_GET['year'] ?? date('Y'));

        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        // Check if event_dates table exists
        $hasEventDates = false;
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE 'event_dates'");
            $hasEventDates = $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $hasEventDates = false;
        }

        if ($hasEventDates) {
            // Use event_dates table for multi-date support
            $stmt = $pdo->prepare("
                SELECT ce.id, ce.title, ce.description, ed.event_date,
                       GROUP_CONCAT(DISTINCT ei.instructor_id) as instructor_ids
                FROM calendar_events ce
                INNER JOIN event_dates ed ON ce.id = ed.event_id
                LEFT JOIN event_instructors ei ON ce.id = ei.event_id
                WHERE ed.event_date BETWEEN :start AND :end
                GROUP BY ce.id, ed.event_date
                ORDER BY ed.event_date ASC
            ");
        } else {
            // Fallback to single event_date column
            $stmt = $pdo->prepare("
                SELECT ce.id, ce.title, ce.description, ce.event_date,
                       GROUP_CONCAT(ei.instructor_id) as instructor_ids
                FROM calendar_events ce
                LEFT JOIN event_instructors ei ON ce.id = ei.event_id
                WHERE ce.event_date BETWEEN :start AND :end
                GROUP BY ce.id
                ORDER BY ce.event_date ASC
            ");
        }
        $stmt->execute([':start' => $startDate, ':end' => $endDate]);
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Process instructor_ids into arrays
        foreach ($events as &$event) {
            $event['instructor_ids'] = $event['instructor_ids'] 
                ? array_map('intval', explode(',', $event['instructor_ids'])) 
                : [];
        }

        echo json_encode(['success' => true, 'events' => $events]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function getEvent($pdo) {
    try {
        $id = intval($_GET['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Event ID required']);
            return;
        }

        $stmt = $pdo->prepare("
            SELECT ce.id, ce.title, ce.description, ce.event_date,
                   GROUP_CONCAT(ei.instructor_id) as instructor_ids
            FROM calendar_events ce
            LEFT JOIN event_instructors ei ON ce.id = ei.event_id
            WHERE ce.id = :id
            GROUP BY ce.id
        ");
        $stmt->execute([':id' => $id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($event) {
            $event['instructor_ids'] = $event['instructor_ids'] 
                ? array_map('intval', explode(',', $event['instructor_ids'])) 
                : [];

            // Get instructor names
            if (!empty($event['instructor_ids'])) {
                $placeholders = implode(',', array_fill(0, count($event['instructor_ids']), '?'));
                $stmt2 = $pdo->prepare("SELECT id, first_name, last_name FROM instructors WHERE id IN ($placeholders)");
                $stmt2->execute($event['instructor_ids']);
                $event['instructors'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $event['instructors'] = [];
            }

            // Get all dates for this event from event_dates table
            $hasEventDates = false;
            try {
                $stmt = $pdo->query("SHOW TABLES LIKE 'event_dates'");
                $hasEventDates = $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                $hasEventDates = false;
            }

            if ($hasEventDates) {
                $stmt2 = $pdo->prepare("SELECT event_date FROM event_dates WHERE event_id = :event_id ORDER BY event_date ASC");
                $stmt2->execute([':event_id' => $id]);
                $event['event_dates'] = $stmt2->fetchAll(PDO::FETCH_COLUMN);
            } else {
                $event['event_dates'] = [$event['event_date']];
            }

            echo json_encode(['success' => true, 'event' => $event]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Event not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function createEvent($pdo) {
    try {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $eventDates = $_POST['event_dates'] ?? [];
        $instructorIds = $_POST['instructor_ids'] ?? [];
        $createdBy = $_SESSION['user_id'] ?? null;

        // Support legacy single date field
        if (empty($eventDates)) {
            $singleDate = $_POST['event_date'] ?? '';
            if (!empty($singleDate)) {
                $eventDates = [$singleDate];
            }
        }

        if (empty($title) || empty($eventDates)) {
            echo json_encode(['success' => false, 'message' => 'Title and at least one date are required']);
            return;
        }

        // Validate all date formats
        foreach ($eventDates as $date) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                echo json_encode(['success' => false, 'message' => 'Invalid date format: ' . $date]);
                return;
            }
        }

        // Sort dates
        sort($eventDates);

        // Use the first date as the primary event_date for backward compatibility
        $primaryDate = $eventDates[0];

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO calendar_events (title, description, event_date, created_by) VALUES (:title, :description, :event_date, :created_by)");
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':event_date' => $primaryDate,
            ':created_by' => $createdBy
        ]);

        $eventId = $pdo->lastInsertId();

        // Insert into event_dates table if it exists
        $hasEventDates = false;
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE 'event_dates'");
            $hasEventDates = $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $hasEventDates = false;
        }

        if ($hasEventDates) {
            $stmt = $pdo->prepare("INSERT INTO event_dates (event_id, event_date) VALUES (:event_id, :event_date)");
            foreach ($eventDates as $date) {
                $stmt->execute([':event_id' => $eventId, ':event_date' => $date]);
            }
        }

        // Insert instructor associations
        if (!empty($instructorIds) && is_array($instructorIds)) {
            $stmt = $pdo->prepare("INSERT INTO event_instructors (event_id, instructor_id) VALUES (:event_id, :instructor_id)");
            foreach ($instructorIds as $instId) {
                $instId = intval($instId);
                if ($instId > 0) {
                    $stmt->execute([':event_id' => $eventId, ':instructor_id' => $instId]);
                }
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Event created successfully', 'event_id' => $eventId]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function updateEvent($pdo) {
    try {
        $id = intval($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $eventDates = $_POST['event_dates'] ?? [];
        $instructorIds = $_POST['instructor_ids'] ?? [];

        // Support legacy single date field
        if (empty($eventDates)) {
            $singleDate = $_POST['event_date'] ?? '';
            if (!empty($singleDate)) {
                $eventDates = [$singleDate];
            }
        }

        if (!$id || empty($title) || empty($eventDates)) {
            echo json_encode(['success' => false, 'message' => 'ID, title and at least one date are required']);
            return;
        }

        // Validate all date formats
        foreach ($eventDates as $date) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                echo json_encode(['success' => false, 'message' => 'Invalid date format: ' . $date]);
                return;
            }
        }

        // Sort dates
        sort($eventDates);

        // Use the first date as the primary event_date for backward compatibility
        $primaryDate = $eventDates[0];

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE calendar_events SET title = :title, description = :description, event_date = :event_date WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':event_date' => $primaryDate,
            ':id' => $id
        ]);

        // Update event_dates table if it exists
        $hasEventDates = false;
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE 'event_dates'");
            $hasEventDates = $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $hasEventDates = false;
        }

        if ($hasEventDates) {
            // Remove old dates
            $stmt = $pdo->prepare("DELETE FROM event_dates WHERE event_id = :event_id");
            $stmt->execute([':event_id' => $id]);

            // Insert new dates
            $stmt = $pdo->prepare("INSERT INTO event_dates (event_id, event_date) VALUES (:event_id, :event_date)");
            foreach ($eventDates as $date) {
                $stmt->execute([':event_id' => $id, ':event_date' => $date]);
            }
        }

        // Remove old instructor associations
        $stmt = $pdo->prepare("DELETE FROM event_instructors WHERE event_id = :event_id");
        $stmt->execute([':event_id' => $id]);

        // Insert new instructor associations
        if (!empty($instructorIds) && is_array($instructorIds)) {
            $stmt = $pdo->prepare("INSERT INTO event_instructors (event_id, instructor_id) VALUES (:event_id, :instructor_id)");
            foreach ($instructorIds as $instId) {
                $instId = intval($instId);
                if ($instId > 0) {
                    $stmt->execute([':event_id' => $id, ':instructor_id' => $instId]);
                }
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Event updated successfully']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function deleteEvent($pdo) {
    try {
        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Event ID required']);
            return;
        }

        // event_instructors and event_dates will be cascade deleted
        $stmt = $pdo->prepare("DELETE FROM calendar_events WHERE id = :id");
        $stmt->execute([':id' => $id]);

        echo json_encode(['success' => true, 'message' => 'Event deleted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function getInstructors($pdo) {
    try {
        $stmt = $pdo->query("SELECT id, first_name, last_name, status FROM instructors ORDER BY first_name, last_name");
        $instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'instructors' => $instructors]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}