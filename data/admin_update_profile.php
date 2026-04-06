<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only admin can update profile
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
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON: ' . json_last_error_msg()]);
    exit;
}

$current_password = $input['current_password'] ?? '';
$current_email = trim($input['current_email'] ?? '');
$new_email = trim($input['new_email'] ?? '');
$new_password = $input['new_password'] ?? '';

if (empty($current_password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Current password is required']);
    exit;
}

if (!isset($pdo) || !$pdo) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$admin_id = $_SESSION['user_id'] ?? null;
if (!$admin_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
    exit;
}

try {
    // Fetch current admin record
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Admin not found']);
        exit;
    }

    // If email provided, verify it matches the account
    if (!empty($current_email)) {
        if (strcasecmp($current_email, $admin['email']) !== 0) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Email does not match our records']);
            exit;
        }
    }

    // Verify current password
    if (!password_verify($current_password, $admin['password'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        exit;
    }

    // If only current_password (and optionally current_email) provided, return verification success
    if (empty($new_email) && empty($new_password)) {
        echo json_encode(['success' => true, 'verified' => true, 'message' => 'Current credentials verified']);
        exit;
    }

    // Prepare update
    $update_fields = [];
    $params = [];

    if (!empty($new_email)) {
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            exit;
        }
        // Check if email already used by another admin
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM admins WHERE email = ? AND id != ?");
        $stmt->execute([$new_email, $admin_id]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Email is already used by another account']);
            exit;
        }
        $update_fields[] = 'email = ?';
        $params[] = $new_email;
    }

    if (!empty($new_password)) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $update_fields[] = 'password = ?';
        $params[] = $hashed;
    }

    if (empty($update_fields)) {
        echo json_encode(['success' => false, 'message' => 'No changes specified']);
        exit;
    }

    $params[] = $admin_id;
    $sql = "UPDATE admins SET " . implode(', ', $update_fields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    if ($stmt->rowCount() > 0) {
        // Update session email if changed
        if (isset($new_email) && in_array('email = ?', $update_fields)) {
            $_SESSION['user_email'] = $new_email;
        }
        echo json_encode(['success' => true, 'message' => 'Account updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes were made']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}