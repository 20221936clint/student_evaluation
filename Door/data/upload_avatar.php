<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$instructor_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

if (!$instructor_id) {
    echo json_encode(['success' => false, 'message' => 'User not logged in', 'session' => $_SESSION]);
    exit;
}

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    $error = $_FILES['avatar']['error'] ?? 'no file';
    echo json_encode(['success' => false, 'message' => 'No file uploaded error: ' . $error]);
    exit;
}

$file = $_FILES['avatar'];
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_size = 2 * 1024 * 1024;

if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type: ' . $file['type']]);
    exit;
}

if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 2MB.']);
    exit;
}

$upload_dir = __DIR__ . '/../../media/instructors/';
if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
        exit;
    }
}

$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'instructor_' . $instructor_id . '_' . time() . '.' . $extension;
$target_path = $upload_dir . $filename;

if (move_uploaded_file($file['tmp_name'], $target_path)) {
    require_once 'config.php';
    
    try {
        $stmt = $pdo->query("DESCRIBE instructors");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!in_array('avatar', $columns)) {
            $pdo->exec("ALTER TABLE instructors ADD COLUMN avatar VARCHAR(255) DEFAULT NULL AFTER birthday");
        }
        
        $stmt = $pdo->prepare("SELECT avatar FROM instructors WHERE id = ?");
        $stmt->execute([$instructor_id]);
        $old_avatar = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare("UPDATE instructors SET avatar = ? WHERE id = ?");
        $stmt->execute([$filename, $instructor_id]);
        
        $verify = $pdo->prepare("SELECT avatar FROM instructors WHERE id = ?");
        $verify->execute([$instructor_id]);
        $new_avatar = $verify->fetchColumn();
        
        if ($old_avatar && file_exists($upload_dir . $old_avatar)) {
            unlink($upload_dir . $old_avatar);
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Profile picture updated',
            'filename' => $filename
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
}
