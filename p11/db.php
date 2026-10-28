<?php
// Practical 11: Database Connection
$host = "localhost";
$username = "root";
$password = "";
$database = "studenthub";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper to set flash message
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'info'
        'message' => $message
    ];
}
?>
