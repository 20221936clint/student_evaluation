<?php
header('Content-Type: application/json');
session_start();

$db_host = 'localhost';
$db_name = 'checkmate';
$db_user = 'root';
$db_pass = '';

$pdo = null;
$db_connected = false;

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db_connected = true;
} catch (PDOException $e) {
    $db_connected = false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($role) || empty($email) || empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fill in all fields'
        ]);
        exit;
    }

    $allowed_roles = ['program_head', 'instructor'];
    if (!in_array($role, $allowed_roles)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid role selected'
        ]);
        exit;
    }
   if (!$db_connected) {
        demoLogin($role, $email, $password);
        exit;
    }

    try {   $table = $role === 'program_head' ? 'program_heads' : 'instructors';
        
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {       $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $role;
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
      $redirect = $role === 'program_head' 
                ? '../Door/program_head/dashboard.php' 
                : '../Door/instructor/dashboard.php';

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => $redirect
            ]);
        } else {          demoLogin($role, $email, $password);
        }
    } catch (Exception $e) {      demoLogin($role, $email, $password);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}

function demoLogin($role, $email, $password) {    $demo_credentials = [
        'program_head' => [
            'email' => 'head@test.com',
            'password' => 'password123'
        ],
        'instructor' => [
            'email' => 'teacher@test.com',
            'password' => 'password123'
        ]
    ];

    $demo_email = $demo_credentials[$role]['email'] ?? '';
    $demo_password = $demo_credentials[$role]['password'] ?? '';

    if ($email === $demo_email && $password === $demo_password) {
        // Set session
        $_SESSION['user_id'] = 1;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = $role;
        $_SESSION['user_name'] = $role === 'program_head' ? 'John Head' : 'Jane Teacher';

        // Redirect based on role
        $redirect = $role === 'program_head' 
            ? '../Door/program_head/dashboard.php' 
            : '../Door/instructor/dashboard.php';

        echo json_encode([
            'success' => true,
            'message' => 'Login successful (Demo Mode)',
            'redirect' => $redirect
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email or password. Try: ' . $demo_email . ' / ' . $demo_password
        ]);
    }
}
