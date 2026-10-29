<?php
mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli("localhost", "root", "");
if ($conn->connect_error) {
    http_response_code(500);
    exit("Database connection failed. Please start MySQL in XAMPP.");
}

$conn->set_charset("utf8mb4");
$conn->query("CREATE DATABASE IF NOT EXISTS `studenthub` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db("studenthub");

// Ensure admins table
$conn->query("CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(80) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Ensure events table
$conn->query("CREATE TABLE IF NOT EXISTS `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `event_date` DATE NOT NULL,
    `venue` VARCHAR(180) NOT NULL,
    `category` VARCHAR(80) NOT NULL DEFAULT 'General',
    `status` ENUM('Open','Closed') NOT NULL DEFAULT 'Open',
    `poster` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Ensure event_audit_logs table
$conn->query("CREATE TABLE IF NOT EXISTS `event_audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT NOT NULL,
    `action` VARCHAR(30) NOT NULL,
    `event_id` INT NOT NULL,
    `details` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(`event_id`),
    INDEX(`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Seed default admin if missing
$chk = $conn->query("SELECT id FROM admins WHERE username='admin' LIMIT 1");
if ($chk && $chk->num_rows === 0) {
    $u = "admin";
    $h = password_hash("admin123", PASSWORD_DEFAULT);
    $s = $conn->prepare("INSERT INTO admins(username, password_hash) VALUES(?, ?)");
    $s->bind_param("ss", $u, $h);
    $s->execute();
    $s->close();
}

// Seed sample events if empty
$chkEvents = $conn->query("SELECT COUNT(*) AS total FROM events");
if ($chkEvents && (int)$chkEvents->fetch_assoc()['total'] === 0) {
    $samples = [
        ["Web Development Workshop", "Hands-on full stack web development workshop covering HTML5, CSS3, JavaScript, PHP and MySQL.", date("Y-m-d", strtotime("+7 days")), "Computer Lab 1", "Workshop", "Open"],
        ["AI & Data Science Hackathon", "24-hour inter-college code hackathon building intelligent agent solutions.", date("Y-m-d", strtotime("+14 days")), "Main Auditorium", "Hackathon", "Open"],
        ["Tech Poster Presentation", "Students display research and capstone project posters.", date("Y-m-d", strtotime("+21 days")), "Exhibition Hall", "Exhibition", "Open"],
        ["Alumni Interactive Session", "Career guidance and industry insights from graduated alumni.", date("Y-m-d", strtotime("-5 days")), "Seminar Room 3", "Seminar", "Closed"]
    ];
    $s = $conn->prepare("INSERT INTO events(title, description, event_date, venue, category, status) VALUES(?,?,?,?,?,?)");
    foreach ($samples as $r) {
        $s->bind_param("ssssss", $r[0], $r[1], $r[2], $r[3], $r[4], $r[5]);
        $s->execute();
    }
    $s->close();
}
?>
