<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "studenthub";
mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($host, $username, $password);
if ($conn->connect_error) {
    http_response_code(500);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'success' => false,
        'error'   => 'Database connection failed: ' . $conn->connect_error . '. Please verify MySQL is running in XAMPP.'
    ]);
    exit();
}
$conn->query("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
$conn->select_db($database);
$conn->set_charset("utf8mb4");
$conn->query("CREATE TABLE IF NOT EXISTS `students` (
  `student_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `course` VARCHAR(100) NOT NULL,
  `semester` INT NOT NULL DEFAULT 1,
  `phone` VARCHAR(15) NOT NULL DEFAULT '',
  `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$countRes = $conn->query("SELECT COUNT(*) AS total FROM `students`");
if ($countRes && (int)$countRes->fetch_assoc()['total'] === 0) {
    $seedSql = "INSERT INTO `students` (`name`, `email`, `course`, `semester`, `phone`, `status`) VALUES
    ('Aarav Patel', 'aarav.patel@charusat.edu.in', 'Computer Engineering', 3, '9825011223', 'Active'),
    ('Diya Shah', 'diya.shah@charusat.edu.in', 'Information Technology', 3, '9825022334', 'Active'),
    ('Krish Mehta', 'krish.mehta@charusat.edu.in', 'Computer Engineering', 4, '9825033445', 'Active'),
    ('Riya Desai', 'riya.desai@charusat.edu.in', 'Information Technology', 3, '9825044556', 'Active'),
    ('Vivaan Joshi', 'vivaan.joshi@charusat.edu.in', 'Computer Engineering', 5, '9825055667', 'Active'),
    ('Ananya Sharma', 'ananya.sharma@charusat.edu.in', 'Mechanical Engineering', 2, '9825066778', 'Inactive'),
    ('Rohan Verma', 'rohan.verma@charusat.edu.in', 'Civil Engineering', 4, '9825077889', 'Active'),
    ('Tanvi Trivedi', 'tanvi.trivedi@charusat.edu.in', 'Information Technology', 5, '9825088990', 'Active'),
    ('Yash Dave', 'yash.dave@charusat.edu.in', 'Electronics & Comm', 3, '9825099001', 'Active'),
    ('Ishaan Bhatt', 'ishaan.bhatt@charusat.edu.in', 'Computer Engineering', 2, '9825100112', 'Active')";
    $conn->query($seedSql);
}
?>
