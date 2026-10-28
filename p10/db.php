<?php
mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli("localhost", "root", "", "studenthub");
if ($conn->connect_error) { http_response_code(500); die("Database connection failed. Check XAMPP and the studenthub database."); }
$conn->set_charset("utf8mb4");
?>