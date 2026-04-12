<?php
// data/upload_avatar.php
header('Content-Type: application/json');
require_once 'config.php';

$instructor_id = isset($_POST['instructor_id']) ? intval($_POST['instructor_id']) : 0;

if ($instructor_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid instructor ID']);
    return;
}

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    return;
}

$file = $_FILES['avatar'];
$allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

if (!in_array($file['type'], $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, GIF, WEBP allowed']);
    return;
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = $instructor_id . '.' . $ext;
$target_dir = __DIR__ . '/../media/instructors/';

if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);
}

$target_path = $target_dir . $filename;

if (move_uploaded_file($file['tmp_name'], $target_path)) {
    echo json_encode(['success' => true, 'message' => 'Avatar uploaded successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save file']);
}