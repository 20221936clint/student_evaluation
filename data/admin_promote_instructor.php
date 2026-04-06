<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$instructor_id = (int)($_POST['instructor_id'] ?? 0);
$admin_id = (int)($_SESSION['user_id'] ?? 0);

if (!$instructor_id || !$admin_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid instructor or admin.']);
    exit;
}

try {
    // Revoke all current program heads
    $pdo->exec("UPDATE admin_promotions SET status = 'revoked' WHERE promoted_to = 'program_head' AND status = 'active'");
    // Promote selected instructor
    $stmt = $pdo->prepare("INSERT INTO admin_promotions (instructor_id, promoted_to, promoted_by, status) VALUES (?, 'program_head', ?, 'active')");
    $stmt->execute([$instructor_id, $admin_id]);
    echo json_encode(['success' => true, 'message' => 'Instructor promoted to Program Head.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
