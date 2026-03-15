<?php
/**
 * Session Security - Include this at the top of every protected page
 * Prevents browser back/forward button access after logout
 */

// Include database config
require_once __DIR__ . '/config.php';

// Start session if not already started, with cookie path set to root
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Prevent browser caching of protected pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

/**
 * Check if user is authenticated with the required role
 * @param string $required_role - The role required to access the page (admin, program_head, instructor)
 * @param string $login_redirect - Path to login page relative to current file
 */
function check_auth($required_role, $login_redirect = '../login.php') {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $required_role) {
        // Session is invalid or role doesn't match - redirect to login
        // Destroy any remaining session
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: ' . $login_redirect);
        exit;
    }
}

/**
 * Check if user has the correct role for the current page
 * Returns an array with access status and information
 * @param string $required_role - The role required to access the page
 * @return array - ['allowed' => boolean, 'current_role' => string, 'required_role' => string, 'message' => string]
 */
function check_role_access($required_role) {
    // User is not logged in at all
    if (!isset($_SESSION['user_role'])) {
        return [
            'allowed' => false,
            'current_role' => 'guest',
            'required_role' => $required_role,
            'message' => 'You need to login to access this page.',
            'action' => 'login'
        ];
    }
    
    // User is logged in but wrong role
    if ($_SESSION['user_role'] !== $required_role) {
        $role_names = [
            'admin' => 'Administrator',
            'program_head' => 'Program Head',
            'instructor' => 'Instructor'
        ];
        $current_role_name = $role_names[$_SESSION['user_role']] ?? $_SESSION['user_role'];
        $required_role_name = $role_names[$required_role] ?? $required_role;
        
        return [
            'allowed' => false,
            'current_role' => $_SESSION['user_role'],
            'required_role' => $required_role,
            'message' => "You are currently logged in as $current_role_name. Please logout and login as $required_role_name to access this page.",
            'action' => 'role_mismatch'
        ];
    }
    
    // User has correct role
    return [
        'allowed' => true,
        'current_role' => $_SESSION['user_role'],
        'required_role' => $required_role,
        'message' => '',
        'action' => 'access_granted'
    ];
}
?>