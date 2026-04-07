<?php
// Door/data/get_student_by_email.php
header('Content-Type: application/json');
require_once 'config.php';

$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$result = ['success' => false, 'student' => null, 'message' => ''];

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email FROM students WHERE email = ?");
    $stmt->execute([$email]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($student) {
        echo json_encode(['success' => true, 'student' => $student]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
