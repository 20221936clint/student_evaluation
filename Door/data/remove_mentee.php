<?php
// Door/data/remove_mentee.php
header('Content-Type: application/json');
require_once 'config.php';

$mentee_id = isset($_POST['mentee_id']) ? intval($_POST['mentee_id']) : 0;
$instructor_id = isset($_POST['instructor_id']) ? intval($_POST['instructor_id']) : 0;

if ($mentee_id <= 0 || $instructor_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM mentees WHERE id = ? AND mentor_id = ?");
    $stmt->execute([$mentee_id, $instructor_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Mentee removed']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Mentee not found or not assigned to this instructor']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
