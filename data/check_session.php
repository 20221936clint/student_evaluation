<?php
/**
 * AJAX endpoint to check if the user's session is still valid
 * Used by session_guard.js to detect expired sessions
 */
header('Content-Type: application/json');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Ensure session cookie is set for the root path so it works across all subdirectories
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

if (isset($_SESSION['user_role']) && isset($_SESSION['user_id'])) {
    echo json_encode([
        'authenticated' => true,
        'role' => $_SESSION['user_role']
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        'authenticated' => false
    ]);
}