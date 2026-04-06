<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only admin can update system settings
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

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$system_name = trim($input['system_name'] ?? '');
$system_tagline = trim($input['system_tagline'] ?? '');

if (empty($system_name)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'System name is required']);
    exit;
}

try {
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }

    // Update the first admin (lowest id) with system settings
    $sql = "UPDATE admins SET system_name = ?, system_tagline = ? ORDER BY id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$system_name, $system_tagline]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'System settings updated']);
    } else {
        // Maybe there are no admins
        echo json_encode(['success' => false, 'message' => 'No admin record found to update']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}