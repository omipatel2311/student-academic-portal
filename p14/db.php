<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "studenthub";
mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($host, $username, $password);
if ($conn->connect_error) {
    http_response_code(500);
    die("Database connection failed. Please ensure MySQL is running in XAMPP: " . $conn->connect_error);
}
$conn->query("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($database);
$conn->set_charset("utf8mb4");
$conn->query("CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(80) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'student') NOT NULL DEFAULT 'student',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `remember_token` VARCHAR(255) DEFAULT NULL,
  `remember_expiry` DATETIME DEFAULT NULL,
  `last_login` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$conn->query("CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `username` VARCHAR(80) NOT NULL,
  `role` VARCHAR(30) NOT NULL,
  `action` VARCHAR(50) NOT NULL,
  `details` VARCHAR(255) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(`user_id`),
  INDEX(`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$eCols = [];
$eRes = $conn->query("SHOW COLUMNS FROM `events`");
if ($eRes) {
    while ($row = $eRes->fetch_assoc()) { $eCols[] = $row['Field']; }
    if (!in_array('status', $eCols)) {
        $conn->query("ALTER TABLE `events` ADD COLUMN `status` ENUM('Open','Closed') NOT NULL DEFAULT 'Open'");
    }
    if (!in_array('category', $eCols)) {
        $conn->query("ALTER TABLE `events` ADD COLUMN `category` VARCHAR(80) NOT NULL DEFAULT 'General'");
    }
    if (!in_array('venue', $eCols)) {
        $conn->query("ALTER TABLE `events` ADD COLUMN `venue` VARCHAR(180) NOT NULL DEFAULT 'Main Campus'");
    }
    if (!in_array('poster', $eCols)) {
        $conn->query("ALTER TABLE `events` ADD COLUMN `poster` VARCHAR(255) DEFAULT NULL");
    }
}
$rCols = [];
$rRes = $conn->query("SHOW COLUMNS FROM `registrations`");
if ($rRes) {
    while ($row = $rRes->fetch_assoc()) { $rCols[] = $row['Field']; }
    if (!in_array('status', $rCols)) {
        $conn->query("ALTER TABLE `registrations` ADD COLUMN `status` VARCHAR(30) NOT NULL DEFAULT 'Confirmed'");
    }
    if (!in_array('student_name', $rCols)) {
        $conn->query("ALTER TABLE `registrations` ADD COLUMN `student_name` VARCHAR(100) NOT NULL DEFAULT ''");
    }
    if (!in_array('student_email', $rCols)) {
        $conn->query("ALTER TABLE `registrations` ADD COLUMN `student_email` VARCHAR(100) NOT NULL DEFAULT ''");
    }
    if (!in_array('event_title', $rCols)) {
        $conn->query("ALTER TABLE `registrations` ADD COLUMN `event_title` VARCHAR(150) NOT NULL DEFAULT ''");
    }
}
$adminExists = $conn->query("SELECT id FROM users WHERE username = 'admin' LIMIT 1");
if ($adminExists && $adminExists->num_rows === 0) {
    $adminHash = password_hash("admin123", PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (username, email, password, role, status) VALUES ('admin', 'admin@charusat.edu.in', '{$adminHash}', 'admin', 'active')");
}
$studentExists = $conn->query("SELECT id FROM users WHERE username = 'student' LIMIT 1");
if ($studentExists && $studentExists->num_rows === 0) {
    $studentHash = password_hash("student123", PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (username, email, password, role, status) VALUES ('student', 'student@charusat.edu.in', '{$studentHash}', 'student', 'active')");
}
?>
