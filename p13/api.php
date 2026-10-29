<?php
/**
 * Practical 13: Centralized PHP JSON API Endpoint
 * Handles CRUD operations asynchronously with JSON request/response formats,
 * HTTP status codes, and server-side validation.
 */

require_once __DIR__ . "/db.php";

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Respond to preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/**
 * Standard JSON Success Response
 */
function send_response(bool $success, mixed $data = null, string $message = '', int $statusCode = 200, array $errors = []): void {
    http_response_code($statusCode);
    echo json_encode([
        'success'   => $success,
        'message'   => $message,
        'data'      => $data,
        'errors'    => $errors,
        'timestamp' => date('c')
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Read JSON body or POST form data
$rawInput = file_get_contents('php://input');
$jsonData = json_decode($rawInput, true) ?? [];
$requestData = array_merge($_POST, $jsonData);

$action = $_GET['action'] ?? ($requestData['action'] ?? 'list');
$method = $_SERVER['REQUEST_METHOD'];

switch ($action) {

    // ----------------------------------------------------
    // 1. LIST STUDENTS (GET) with Search and Filter
    // ----------------------------------------------------
    case 'list':
        $search = trim($_GET['search'] ?? '');
        $course = trim($_GET['course'] ?? '');
        $status = trim($_GET['status'] ?? '');

        $where = [];
        $params = [];
        $types = '';

        if ($search !== '') {
            $where[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "sss";
        }

        if ($course !== '') {
            $where[] = "course = ?";
            $params[] = $course;
            $types .= "s";
        }

        if ($status !== '') {
            $where[] = "status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $sql = "SELECT student_id, name, email, course, semester, phone, status, created_at FROM students";
        if (count($where) > 0) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY student_id DESC";

        $stmt = $conn->prepare($sql);
        if ($stmt && count($params) > 0) {
            $stmt->bind_param($types, ...$params);
        }

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $students = [];
            while ($row = $result->fetch_assoc()) {
                $students[] = [
                    'student_id' => (int)$row['student_id'],
                    'name'       => $row['name'],
                    'email'      => $row['email'],
                    'course'     => $row['course'],
                    'semester'   => (int)$row['semester'],
                    'phone'      => $row['phone'],
                    'status'     => $row['status'],
                    'created_at' => $row['created_at']
                ];
            }
            $stmt->close();

            // Compute summary stats
            $totalCount = count($students);
            $activeCount = count(array_filter($students, fn($s) => $s['status'] === 'Active'));

            send_response(true, [
                'students' => $students,
                'stats' => [
                    'total'  => $totalCount,
                    'active' => $activeCount
                ]
            ], "Fetched {$totalCount} students successfully.", 200);
        } else {
            send_response(false, null, "Failed to execute database query: " . $conn->error, 500);
        }
        break;

    // ----------------------------------------------------
    // 2. GET SINGLE STUDENT (GET)
    // ----------------------------------------------------
    case 'get':
        $id = (int)($_GET['id'] ?? ($requestData['id'] ?? 0));
        if ($id <= 0) {
            send_response(false, null, "Invalid student ID provided.", 400);
        }

        $stmt = $conn->prepare("SELECT student_id, name, email, course, semester, phone, status FROM students WHERE student_id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $student = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($student) {
            $student['student_id'] = (int)$student['student_id'];
            $student['semester'] = (int)$student['semester'];
            send_response(true, $student, "Student details retrieved.", 200);
        } else {
            send_response(false, null, "Student record not found.", 404);
        }
        break;

    // ----------------------------------------------------
    // 3. CREATE STUDENT (POST)
    // ----------------------------------------------------
    case 'create':
        if ($method !== 'POST') {
            send_response(false, null, "Method Not Allowed. Use POST.", 405);
        }

        $name     = trim((string)($requestData['name'] ?? ''));
        $email    = trim((string)($requestData['email'] ?? ''));
        $course   = trim((string)($requestData['course'] ?? ''));
        $semester = (int)($requestData['semester'] ?? 1);
        $phone    = trim((string)($requestData['phone'] ?? ''));
        $status   = in_array($requestData['status'] ?? '', ['Active', 'Inactive']) ? $requestData['status'] : 'Active';

        $errors = [];
        if ($name === '') $errors['name'] = 'Student name is required.';
        if ($email === '') {
            $errors['email'] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email address format.';
        }
        if ($course === '') $errors['course'] = 'Course selection is required.';
        if ($semester < 1 || $semester > 8) $errors['semester'] = 'Semester must be between 1 and 8.';
        if ($phone === '') {
            $errors['phone'] = 'Mobile number is required.';
        } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
            $errors['phone'] = 'Mobile number must be exactly 10 digits.';
        }

        if (count($errors) > 0) {
            send_response(false, null, "Validation failed.", 422, $errors);
        }

        // Duplicate check
        $dupStmt = $conn->prepare("SELECT student_id FROM students WHERE email = ? LIMIT 1");
        $dupStmt->bind_param("s", $email);
        $dupStmt->execute();
        if ($dupStmt->get_result()->num_rows > 0) {
            $dupStmt->close();
            send_response(false, null, "A student with email '{$email}' already exists.", 409, ['email' => 'Duplicate email address']);
        }
        $dupStmt->close();

        $ins = $conn->prepare("INSERT INTO students (name, email, course, semester, phone, status) VALUES (?, ?, ?, ?, ?, ?)");
        $ins->bind_param("sssiis", $name, $email, $course, $semester, $phone, $status);

        if ($ins->execute()) {
            $newId = (int)$conn->insert_id;
            $ins->close();
            send_response(true, [
                'student_id' => $newId,
                'name'       => $name,
                'email'      => $email,
                'course'     => $course,
                'semester'   => $semester,
                'phone'      => $phone,
                'status'     => $status
            ], "Student '{$name}' created successfully!", 201);
        } else {
            send_response(false, null, "Database error: " . $conn->error, 500);
        }
        break;

    // ----------------------------------------------------
    // 4. UPDATE STUDENT (POST)
    // ----------------------------------------------------
    case 'update':
        if ($method !== 'POST') {
            send_response(false, null, "Method Not Allowed. Use POST.", 405);
        }

        $id       = (int)($requestData['student_id'] ?? ($_GET['id'] ?? 0));
        $name     = trim((string)($requestData['name'] ?? ''));
        $email    = trim((string)($requestData['email'] ?? ''));
        $course   = trim((string)($requestData['course'] ?? ''));
        $semester = (int)($requestData['semester'] ?? 1);
        $phone    = trim((string)($requestData['phone'] ?? ''));
        $status   = in_array($requestData['status'] ?? '', ['Active', 'Inactive']) ? $requestData['status'] : 'Active';

        if ($id <= 0) {
            send_response(false, null, "Invalid student ID.", 400);
        }

        $errors = [];
        if ($name === '') $errors['name'] = 'Student name is required.';
        if ($email === '') {
            $errors['email'] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email address format.';
        }
        if ($course === '') $errors['course'] = 'Course selection is required.';
        if ($semester < 1 || $semester > 8) $errors['semester'] = 'Semester must be between 1 and 8.';
        if ($phone === '') {
            $errors['phone'] = 'Mobile number is required.';
        } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
            $errors['phone'] = 'Mobile number must be exactly 10 digits.';
        }

        if (count($errors) > 0) {
            send_response(false, null, "Validation failed.", 422, $errors);
        }

        // Duplicate email check for other records
        $dupStmt = $conn->prepare("SELECT student_id FROM students WHERE email = ? AND student_id != ? LIMIT 1");
        $dupStmt->bind_param("si", $email, $id);
        $dupStmt->execute();
        if ($dupStmt->get_result()->num_rows > 0) {
            $dupStmt->close();
            send_response(false, null, "Another student is already using this email address.", 409, ['email' => 'Duplicate email address']);
        }
        $dupStmt->close();

        $up = $conn->prepare("UPDATE students SET name = ?, email = ?, course = ?, semester = ?, phone = ?, status = ? WHERE student_id = ?");
        $up->bind_param("sssiisi", $name, $email, $course, $semester, $phone, $status, $id);

        if ($up->execute()) {
            $up->close();
            send_response(true, [
                'student_id' => $id,
                'name'       => $name,
                'email'      => $email,
                'course'     => $course,
                'semester'   => $semester,
                'phone'      => $phone,
                'status'     => $status
            ], "Student '{$name}' updated successfully!", 200);
        } else {
            send_response(false, null, "Database update error: " . $conn->error, 500);
        }
        break;

    // ----------------------------------------------------
    // 5. DELETE STUDENT (POST)
    // ----------------------------------------------------
    case 'delete':
        if ($method !== 'POST') {
            send_response(false, null, "Method Not Allowed. Use POST.", 405);
        }

        $id = (int)($requestData['student_id'] ?? ($_GET['id'] ?? 0));
        if ($id <= 0) {
            send_response(false, null, "Invalid student ID specified.", 400);
        }

        $del = $conn->prepare("DELETE FROM students WHERE student_id = ?");
        $del->bind_param("i", $id);
        if ($del->execute()) {
            if ($del->affected_rows > 0) {
                $del->close();
                send_response(true, ['student_id' => $id], "Student #{$id} deleted successfully.", 200);
            } else {
                $del->close();
                send_response(false, null, "Student record not found or already deleted.", 404);
            }
        } else {
            send_response(false, null, "Database delete error: " . $conn->error, 500);
        }
        break;

    default:
        send_response(false, null, "Unknown API action: {$action}", 400);
        break;
}
?>
