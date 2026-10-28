<?php
require_once "db.php";

$search = trim($_GET['search'] ?? '');
$course_filter = trim($_GET['course'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

$whereClauses = [];
$params = [];
$types = "";

if ($search !== '') {
    $whereClauses[] = "(name LIKE ? OR email LIKE ? OR course LIKE ?)";
    $likeSearch = "%" . $search . "%";
    $params[] = $likeSearch;
    $params[] = $likeSearch;
    $params[] = $likeSearch;
    $types .= "sss";
}

if ($course_filter !== '') {
    $whereClauses[] = "course = ?";
    $params[] = $course_filter;
    $types .= "s";
}

if ($status_filter !== '') {
    $whereClauses[] = "status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$sql = "SELECT student_id, name, email, course, semester, phone, status, created_at FROM students";
if (count($whereClauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}
$sql .= " ORDER BY student_id ASC";

$stmt = $conn->prepare($sql);
if ($stmt && count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

$filename = "studenthub_students_" . date('Y-m-d_His') . ".csv";

// Send HTTP headers to force file download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// CSV Column Headers
fputcsv($output, ['Student ID', 'Full Name', 'Email Address', 'Course', 'Semester', 'Phone', 'Enrollment Status', 'Date Added']);

// Write data rows
if ($result) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['student_id'],
            $row['name'],
            $row['email'],
            $row['course'],
            $row['semester'],
            $row['phone'],
            $row['status'],
            $row['created_at']
        ]);
    }
}

fclose($output);
exit();
?>
