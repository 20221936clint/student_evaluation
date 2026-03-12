<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../Door/login.php');
    exit;
}

$action = $_GET['action'] ?? '';

// Database connection
$host = 'localhost';
$dbname = 'checkmate';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // For demo purposes, continue without database
    $pdo = null;
}

if ($action === 'add_instructor') {
    // Handle add instructor form submission
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $suffix = trim($_POST['suffix'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $department = $_POST['department'] ?? '';
    $position = $_POST['position'] ?? 'Instructor';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($department) || empty($password)) {
        header('Location: ../Door/admin/dashboard.php?page=add_program_head&error=' . urlencode('Please fill in all required fields'));
        exit;
    }

    if ($password !== $confirm_password) {
        header('Location: ../Door/admin/dashboard.php?page=add_program_head&error=' . urlencode('Passwords do not match'));
        exit;
    }

    if (strlen($password) < 6) {
        header('Location: ../Door/admin/dashboard.php?page=add_program_head&error=' . urlencode('Password must be at least 6 characters'));
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Generate employee ID
    $employee_id = 'EMP' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

    if ($pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO instructors (first_name, middle_name, last_name, suffix, email, password, department, employee_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$first_name, $middle_name, $last_name, $suffix, $email, $hashed_password, $department, $employee_id]);
            header('Location: ../Door/admin/dashboard.php?page=manage_program_heads&success=' . urlencode('Instructor added successfully!'));
        } catch (PDOException $e) {
            header('Location: ../Door/admin/dashboard.php?page=add_program_head&error=' . urlencode('Email already exists'));
        }
    } else {
        // For demo, just redirect with success
        header('Location: ../Door/admin/dashboard.php?page=manage_program_heads&success=' . urlencode('Instructor added successfully! (Demo Mode)'));
    }
    exit;

} elseif ($action === 'add_program_head') {
    // Handle add program head form submission
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $department = $_POST['department'] ?? '';
    $position = $_POST['position'] ?? 'Program Head';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($department) || empty($password)) {
        header('Location: ../Door/admin/dashboard.php?page=add_program_head&error=' . urlencode('Please fill in all required fields'));
        exit;
    }

    if ($password !== $confirm_password) {
        header('Location: ../Door/admin/dashboard.php?page=add_program_head&error=' . urlencode('Passwords do not match'));
        exit;
    }

    if (strlen($password) < 6) {
        header('Location: ../Door/admin/dashboard.php?page=add_program_head&error=' . urlencode('Password must be at least 6 characters'));
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if ($pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO program_heads (first_name, last_name, email, password, department) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $email, $hashed_password, $department]);
            header('Location: ../Door/admin/dashboard.php?page=manage_program_heads&success=' . urlencode('Program head added successfully!'));
        } catch (PDOException $e) {
            header('Location: ../Door/admin/dashboard.php?page=add_program_head&error=' . urlencode('Email already exists'));
        }
    } else {
        // For demo, just redirect with success
        header('Location: ../Door/admin/dashboard.php?page=manage_program_heads&success=' . urlencode('Program head added successfully! (Demo Mode)'));
    }
    exit;

} elseif ($action === 'remove_program_head') {
    $id = $_GET['id'] ?? 0;

    if (empty($id)) {
        header('Location: ../Door/admin/dashboard.php?page=remove_program_head&error=' . urlencode('Invalid program head ID'));
        exit;
    }

    if ($pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM program_heads WHERE id = ?");
            $stmt->execute([$id]);
            header('Location: ../Door/admin/dashboard.php?page=manage_program_heads&success=' . urlencode('Program head removed successfully!'));
        } catch (PDOException $e) {
            header('Location: ../Door/admin/dashboard.php?page=manage_program_heads&error=' . urlencode('Failed to remove program head'));
        }
    } else {
        // For demo, just redirect with success
        header('Location: ../Door/admin/dashboard.php?page=manage_program_heads&success=' . urlencode('Program head removed successfully! (Demo Mode)'));
    }
    exit;

} elseif ($action === 'quick_add') {
    $type = $_GET['type'] ?? '';

    if ($type === 'demo') {
        // Add demo instructors if database is available
        if ($pdo) {
            $demo_instructors = [
                ['John', 'Harris', 'john.harris@cjcm.edu', 'Operational Management'],
                ['Sarah', 'Miller', 'sarah.miller@cjcm.edu', 'Financial Management'],
                ['Robert', 'Wilson', 'robert.wilson@cjcm.edu', 'Marketing Management']
            ];
            
            $hashed_password = password_hash('password123', PASSWORD_DEFAULT);
            
            foreach ($demo_instructors as $instructor) {
                $employee_id = 'EMP' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
                try {
                    $stmt = $pdo->prepare("INSERT INTO instructors (first_name, last_name, email, password, department, employee_id) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$instructor[0], $instructor[1], $instructor[2], $hashed_password, $instructor[3], $employee_id]);
                } catch (PDOException $e) {
                    // Skip if already exists
                }
            }
        }
        header('Location: ../Door/admin/dashboard.php?page=manage_program_heads&success=' . urlencode('Demo instructors added successfully!'));
    } else {
        header('Location: ../Door/admin/dashboard.php?page=manage_program_heads');
    }
    exit;

} elseif ($action === 'remove_instructor') {
    $id = $_GET['id'] ?? 0;

    if (empty($id)) {
        header('Location: ../Door/admin/dashboard.php?page=manage_program_heads&error=' . urlencode('Invalid instructor ID'));
        exit;
    }

    if ($pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM instructors WHERE id = ?");
            $stmt->execute([$id]);
            header('Location: ../Door/admin/dashboard.php?page=manage_program_heads&success=' . urlencode('Instructor removed successfully!'));
        } catch (PDOException $e) {
            header('Location: ../Door/admin/dashboard.php?page=manage_program_heads&error=' . urlencode('Failed to remove instructor'));
        }
    } else {
        header('Location: ../Door/admin/dashboard.php?page=manage_program_heads&success=' . urlencode('Instructor removed successfully! (Demo Mode)'));
    }
    exit;

} elseif ($action === 'edit_instructor') {
    $id = $_POST['id'] ?? 0;
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $suffix = trim($_POST['suffix'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $department = $_POST['department'] ?? '';
    $position = $_POST['position'] ?? 'Instructor';

    if (empty($id) || empty($first_name) || empty($last_name) || empty($email) || empty($department)) {
        header('Location: ../Door/admin/dashboard.php?page=manage_program_heads&error=' . urlencode('Please fill in all required fields'));
        exit;
    }

    if ($pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE instructors SET first_name = ?, middle_name = ?, last_name = ?, suffix = ?, email = ?, department = ?, position = ? WHERE id = ?");
            $stmt->execute([$first_name, $middle_name, $last_name, $suffix, $email, $department, $position, $id]);
            header('Location: ../Door/admin/dashboard.php?page=manage_program_heads&success=' . urlencode('Instructor updated successfully!'));
        } catch (PDOException $e) {
            header('Location: ../Door/admin/dashboard.php?page=manage_program_heads&error=' . urlencode('Failed to update instructor'));
        }
    } else {
        header('Location: ../Door/admin/dashboard.php?page=manage_program_heads&success=' . urlencode('Instructor updated successfully! (Demo Mode)'));
    }
    exit;

} elseif ($action === 'get_instructor') {
    $id = $_GET['id'] ?? 0;

    header('Content-Type: application/json');

    // Validate that id is a positive integer
    if (empty($id) || !is_numeric($id) || intval($id) <= 0) {
        echo json_encode(['error' => 'Invalid instructor ID']);
        exit;
    }

    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM instructors WHERE id = ?");
            $stmt->execute([$id]);
            $instructor = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($instructor === false) {
                echo json_encode(['error' => 'Instructor not found']);
            } else {
                // Remove password from response for security
                unset($instructor['password']);
                echo json_encode($instructor);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Database error occurred']);
        }
    } else {
        echo json_encode(['error' => 'Database connection not available']);
    }
    exit;

} else {
    header('Location: ../Door/admin/dashboard.php');
    exit;
}
