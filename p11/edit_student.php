<?php
require_once "db.php";

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('danger', "Invalid student ID specified.");
    header("Location: index.php");
    exit();
}

$error = '';

// Fetch existing student
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    set_flash('danger', "Student record not found.");
    header("Location: index.php");
    exit();
}

$student = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $semester = (int)($_POST['semester'] ?? 1);
    $phone = trim($_POST['phone'] ?? '');
    $status = in_array($_POST['status'] ?? '', ['Active', 'Inactive']) ? $_POST['status'] : 'Active';

    if (empty($name) || empty($email) || empty($course) || empty($phone)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif ($semester < 1 || $semester > 8) {
        $error = "Semester must be between 1 and 8.";
    } else {
        // Check if another student has the same email
        $checkStmt = $conn->prepare("SELECT student_id FROM students WHERE email = ? AND student_id != ?");
        $checkStmt->bind_param("si", $email, $id);
        $checkStmt->execute();
        $checkRes = $checkStmt->get_result();

        if ($checkRes->num_rows > 0) {
            $error = "Another student is already using this email address.";
            $checkStmt->close();
        } else {
            $checkStmt->close();

            // Prepared UPDATE statement
            $updateStmt = $conn->prepare("UPDATE students SET name = ?, email = ?, course = ?, semester = ?, phone = ?, status = ? WHERE student_id = ?");
            $updateStmt->bind_param("sssiisi", $name, $email, $course, $semester, $phone, $status, $id);

            if ($updateStmt->execute()) {
                set_flash('success', "Student '{$name}' updated successfully!");
                $updateStmt->close();
                header("Location: index.php");
                exit();
            } else {
                $error = "Failed to update record. Error: " . $conn->error;
            }
            $updateStmt->close();
        }
    }
} else {
    $name = $student['name'];
    $email = $student['email'];
    $course = $student['course'];
    $semester = $student['semester'];
    $phone = $student['phone'];
    $status = $student['status'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub | Edit Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="header">
        <div class="container header-content">
            <div>
                <h1>StudentHub Portal</h1>
                <p>Practical 11: Edit Student #<?php echo $id; ?></p>
            </div>
            <div class="header-badge">Update Record</div>
        </div>
    </header>

    <nav class="navbar">
        <div class="container nav-links">
            <div>
                <a href="index.php">&larr; Back to Student Directory</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="card-box form-container">
            <h2 class="card-title">✏️ Edit Student Information</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="edit_student.php?id=<?php echo $id; ?>" method="POST">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Student Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
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
                    <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($phone); ?>" required>
                </div>

                <div class="form-group">
                    <label for="status">Enrollment Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="Active" <?php if ($status === 'Active') echo 'selected'; ?>>Active</option>
                        <option value="Inactive" <?php if ($status === 'Inactive') echo 'selected'; ?>>Inactive</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 25px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Update Student</button>
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
