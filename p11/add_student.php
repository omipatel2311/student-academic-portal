<?php
require_once "db.php";

$error = '';
$name = '';
$email = '';
$course = 'Computer Engineering';
$semester = 1;
$phone = '';
$status = 'Active';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $semester = (int)($_POST['semester'] ?? 1);
    $phone = trim($_POST['phone'] ?? '');
    $status = in_array($_POST['status'] ?? '', ['Active', 'Inactive']) ? $_POST['status'] : 'Active';

    // Server-side validation
    if (empty($name) || empty($email) || empty($course) || empty($phone)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif ($semester < 1 || $semester > 8) {
        $error = "Semester must be between 1 and 8.";
    } else {
        // Check for duplicate email
        $checkStmt = $conn->prepare("SELECT student_id FROM students WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $error = "A student with this email address already exists.";
            $checkStmt->close();
        } else {
            $checkStmt->close();
            // Insert student with prepared statement
            $insertStmt = $conn->prepare("INSERT INTO students (name, email, course, semester, phone, status) VALUES (?, ?, ?, ?, ?, ?)");
            $insertStmt->bind_param("sssiis", $name, $email, $course, $semester, $phone, $status);

            if ($insertStmt->execute()) {
                set_flash('success', "Student '{$name}' registered successfully!");
                $insertStmt->close();
                header("Location: index.php");
                exit();
            } else {
                $error = "Failed to add student. Please try again. Error: " . $conn->error;
            }
            $insertStmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub | Add New Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="header">
        <div class="container header-content">
            <div>
                <h1>StudentHub Portal</h1>
                <p>Practical 11: Add New Student Record</p>
            </div>
            <div class="header-badge">Add Student</div>
        </div>
    </header>

    <nav class="navbar">
        <div class="container nav-links">
            <div>
                <a href="index.php">&larr; Back to Student Records</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="card-box form-container">
            <h2 class="card-title">➕ Add Student Details</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="add_student.php" method="POST">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Aarav Patel" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Student Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="e.g. aarav.patel@charusat.edu.in" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="course">Academic Program / Course *</label>
                    <select id="course" name="course" class="form-control" required>
                        <option value="Computer Engineering" <?php if ($course === 'Computer Engineering') echo 'selected'; ?>>Computer Engineering</option>
                        <option value="Information Technology" <?php if ($course === 'Information Technology') echo 'selected'; ?>>Information Technology</option>
                        <option value="Electronics & Comm" <?php if ($course === 'Electronics & Comm') echo 'selected'; ?>>Electronics & Comm</option>
                        <option value="Mechanical Engineering" <?php if ($course === 'Mechanical Engineering') echo 'selected'; ?>>Mechanical Engineering</option>
                        <option value="Civil Engineering" <?php if ($course === 'Civil Engineering') echo 'selected'; ?>>Civil Engineering</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="semester">Current Semester (1 - 8) *</label>
                    <input type="number" id="semester" name="semester" class="form-control" min="1" max="8" value="<?php echo htmlspecialchars((string)$semester); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Contact Number *</label>
                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="10-digit mobile number" value="<?php echo htmlspecialchars($phone); ?>" required>
                </div>

                <div class="form-group">
                    <label for="status">Enrollment Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="Active" <?php if ($status === 'Active') echo 'selected'; ?>>Active</option>
                        <option value="Inactive" <?php if ($status === 'Inactive') echo 'selected'; ?>>Inactive</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 25px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Save Student</button>
                    <a href="index.php" class="btn btn-secondary" style="flex: 1;">Cancel</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026-27 StudentHub Portal - Practical 11</p>
        </div>
    </footer>

</body>
</html>
