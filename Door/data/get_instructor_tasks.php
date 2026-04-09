<?php
session_start();
error_reporting(1);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'instructor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

$instructor_id = $_SESSION['user_id'];

require_once 'config.php';

try {
    // Check if tables exist
    $stmt = $pdo->query("SHOW TABLES LIKE 'tasks'");
    if (!$stmt->fetch()) {
        echo json_encode(['success' => true, 'tasks' => []]);
        exit;
    }
    
    // First, let's check what tasks exist for this instructor
    $debugStmt = $pdo->prepare("SELECT * FROM tasks WHERE instructor_id = ?");
    $debugStmt->execute([$instructor_id]);
    $debugTasks = $debugStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If no tasks found, try with instructors.user_id
    if (empty($debugTasks)) {
        $debugStmt2 = $pdo->query("SELECT id FROM instructors WHERE user_id = " . intval($instructor_id));
        $instructorRow = $debugStmt2->fetch(PDO::FETCH_ASSOC);
        if ($instructorRow) {
            $debugStmt3 = $pdo->prepare("SELECT * FROM tasks WHERE instructor_id = ?");
            $debugStmt3->execute([$instructorRow['id']]);
            $debugTasks = $debugStmt3->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    if (empty($debugTasks)) {
        echo json_encode(['success' => true, 'tasks' => [], 'debug' => 'No tasks found for instructor_id: ' . $instructor_id]);
        exit;
    }
    
    // Fetch tasks with their assignments and mentee details
    $stmt = $pdo->prepare("
        SELECT 
            t.id as task_id,
            t.title,
            t.description,
            t.priority,
            t.due_date,
            t.status as task_status,
            t.created_at,
            COUNT(ta.id) as assigned_count
        FROM tasks t
        LEFT JOIN task_assignments ta ON t.id = ta.task_id
        WHERE t.instructor_id = ?
        GROUP BY t.id
        ORDER BY 
            CASE t.priority 
                WHEN 'high' THEN 1
                WHEN 'medium' THEN 2
                WHEN 'low' THEN 3
            END,
            t.due_date ASC,
            t.created_at DESC
    ");
    
    $stmt->execute([$instructor_id]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch individual mentees for each task
    foreach ($tasks as &$task) {
        $stmt2 = $pdo->prepare("
            SELECT 
                m.id as mentee_id,
                s.first_name,
                s.last_name,
                s.email,
                s.student_id,
                ta.status as assignment_status,
                ta.completion_date
            FROM task_assignments ta
            JOIN mentees m ON ta.mentee_id = m.id
            JOIN students s ON m.student_id = s.id
            WHERE ta.task_id = ?
            ORDER BY s.last_name, s.first_name
        ");
        $stmt2->execute([$task['task_id']]);
        $task['mentees'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode([
        'success' => true,
        'tasks' => $tasks,
        'debug' => 'Found ' . count($tasks) . ' tasks'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
