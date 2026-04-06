<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check if user is logged in as admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Optionally check if setup is required - but we can allow only if is_demo still true
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);
if (empty($input)) {
    $input = $_POST;
}

$newEmail = trim($input['new_email'] ?? '');
$newPassword = $input['new_password'] ?? '';
$confirmPassword = $input['confirm_password'] ?? '';

// Validation
$errors = [];
if (empty($newEmail)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}
if (empty($newPassword)) {
    $errors[] = 'Password is required';
} elseif (strlen($newPassword) < 6) {
    $errors[] = 'Password must be at least 6 characters';
}
if ($newPassword !== $confirmPassword) {
    $errors[] = 'Passwords do not match';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }

    // Check if the new email is already used by another admin (excluding current)
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM admins WHERE email = ? AND id != ?");
    $stmt->execute([$newEmail, $userId]);
    $result = $stmt->fetch();
    if ($result['count'] > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email is already used by another account']);
        exit;
    }

    // Update admin record
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateSql = "UPDATE admins SET email = ?, password = ?, is_demo = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = $pdo->prepare($updateSql);
    $stmt->execute([$newEmail, $hashedPassword, $userId]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('No changes made');
    }

    // Clear session flag and update session email
    unset($_SESSION['admin_requires_setup']);
    $_SESSION['user_email'] = $newEmail;
    $_SESSION['user_name'] = $_SESSION['user_name']; // name unchanged

    echo json_encode([
        'success' => true,
        'message' => 'Account setup completed successfully. You can now use your permanent credentials.'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit;
}