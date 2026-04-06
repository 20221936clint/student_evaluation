<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database configuration
require_once __DIR__ . '/config.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get and validate input data
$input = json_decode(file_get_contents('php://input'), true);

// If not JSON, fallback to form data
if (empty($input)) {
    $input = $_POST;
}

$required_fields = ['first_name', 'last_name', 'email', 'password', 'confirm_password'];
$errors = [];

foreach ($required_fields as $field) {
    if (empty(trim($input[$field] ?? ''))) {
        $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
    }
}

if (!empty($input['email']) && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if (strlen($input['password'] ?? '') < 6) {
    $errors[] = 'Password must be at least 6 characters';
}

if (($input['password'] ?? '') !== ($input['confirm_password'] ?? '')) {
    $errors[] = 'Passwords do not match';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Sanitize input
$first_name = trim($input['first_name']);
$middle_name = isset($input['middle_name']) ? trim($input['middle_name']) : null;
$last_name = trim($input['last_name']);
$suffix = isset($input['suffix']) && !empty($input['suffix']) ? trim($input['suffix']) : null;
$email = filter_var(trim($input['email']), FILTER_SANITIZE_EMAIL);
$phone = isset($input['phone']) ? trim($input['phone']) : null;
$password = $input['password'];

// Check if email already exists in instructors or pending_instructors
try {
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }

    $checkSql = "SELECT COUNT(*) as count FROM (
        SELECT email FROM instructors WHERE email = ?
        UNION
        SELECT email FROM pending_instructors WHERE email = ?
    ) as combined";
    $stmt = $pdo->prepare($checkSql);
    $stmt->execute([$email, $email]);
    $result = $stmt->fetch();

    if ($result['count'] > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }

    // Insert into pending_instructors
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $status = 'pending';

    $insertSql = "INSERT INTO pending_instructors
        (first_name, middle_name, last_name, suffix, email, password, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($insertSql);
    $stmt->execute([
        $first_name,
        $middle_name,
        $last_name,
        $suffix,
        $email,
        $hashed_password,
        $status
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Registration successful! Your account is pending approval.',
        'data' => [
            'email' => $email,
            'name' => $first_name . ' ' . $last_name
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit;
}