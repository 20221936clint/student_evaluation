<?php
// Database Configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'checkmate';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    // If database connection fails, set conn to null so pages can handle gracefully
    $conn = null;
}

// Set charset
if ($conn) {
    $conn->set_charset("utf8mb4");
}
?>