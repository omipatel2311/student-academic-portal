<?php
require_once "db.php";

$search = trim($_GET['search'] ?? '');
$course_filter = trim($_GET['course'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

// Build dynamic WHERE clause with prepared statement
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

// Pagination: show 10 records per page
$perPage = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

// Count matching records for pagination
$countSql = "SELECT COUNT(*) AS total FROM students";
if (count($whereClauses) > 0) {
    $countSql .= " WHERE " . implode(" AND ", $whereClauses);
}
$countStmt = $conn->prepare($countSql);
if ($countStmt && count($params) > 0) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRows = (int)$countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
$totalPages = max(1, (int)ceil($totalRows / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

$sql = "SELECT student_id, name, email, course, semester, phone, status, created_at FROM students";
if (count($whereClauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}
$sql .= " ORDER BY student_id ASC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $pageParams = $params;
    $pageParams[] = $perPage;
    $pageParams[] = $offset;
    $pageTypes = $types . "ii";
    $stmt->bind_param($pageTypes, ...$pageParams);
}
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

// Fetch list of distinct courses for filter dropdown
$courseList = [];
$courseQuery = $conn->query("SELECT DISTINCT course FROM students ORDER BY course ASC");
if ($courseQuery) {
    while ($cRow = $courseQuery->fetch_assoc()) {
        $courseList[] = $cRow['course'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub | Student Management CRUD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="header">
        <div class="container header-content">
            <div>
                <h1>StudentHub Portal</h1>
                <p>Practical 11: Student Management CRUD with Search & Filter</p>
            </div>
            <div class="header-badge">ITUE203 Practical 11</div>
        </div>
    </header>

    <nav class="navbar">
        <div class="container nav-links">
            <div>
                <a href="index.php" class="active">Student Records</a>
                <a href="add_student.php">Add New Student</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="card-box">
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-<?php echo htmlspecialchars($_SESSION['flash']['type']); ?>">
                    <?php 
                        echo htmlspecialchars($_SESSION['flash']['message']); 
                        unset($_SESSION['flash']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="page-header-row">
                <div>
                    <h2 class="page-title">🎓 Student Directory</h2>
                    <p style="color: #6b7280; font-size: 14px;">Manage student records using MySQLi prepared statements.</p>
                </div>
                <div style="display: flex; gap: 8px;">
                    <a href="add_student.php" class="btn btn-success">➕ Add Student</a>
                    <a href="export_csv.php?search=<?php echo urlencode($search); ?>&course=<?php echo urlencode($course_filter); ?>&status=<?php echo urlencode($status_filter); ?>" class="btn btn-secondary">📥 Export to CSV</a>
                </div>
            </div>

            <!-- Search and Filter Form -->
            <form action="index.php" method="GET" class="filter-form">
                <input type="text" name="search" class="filter-input" placeholder="🔍 Search name, email, course..." value="<?php echo htmlspecialchars($search); ?>">

                <select name="course" class="filter-select">
                    <option value="">All Courses</option>
                    <?php foreach ($courseList as $crs): ?>
                        <option value="<?php echo htmlspecialchars($crs); ?>" <?php if ($course_filter === $crs) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($crs); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="status" class="filter-select" style="min-width: 130px;">
                    <option value="">All Statuses</option>
                    <option value="Active" <?php if ($status_filter === 'Active') echo 'selected'; ?>>Active</option>
                    <option value="Inactive" <?php if ($status_filter === 'Inactive') echo 'selected'; ?>>Inactive</option>
                </select>

                <button type="submit" class="btn btn-primary">Filter</button>
                <?php if ($search !== '' || $course_filter !== '' || $status_filter !== ''): ?>
                    <a href="index.php" class="btn btn-secondary">Reset</a>
                <?php endif; ?>
            </form>

            <!-- Table of Students -->
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Course</th>
                            <th>Sem</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?php echo htmlspecialchars((string)$row['student_id']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['course']); ?></td>
                                    <td>Sem <?php echo htmlspecialchars((string)$row['semester']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $row['status'] === 'Active' ? 'badge-active' : 'badge-inactive'; ?>">
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center; white-space: nowrap;">
                                        <a href="edit_student.php?id=<?php echo $row['student_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                        
                                        <!-- Soft Delete Toggle -->
                                        <a href="delete_student.php?id=<?php echo $row['student_id']; ?>&action=toggle_status" class="btn btn-sm btn-warning" title="Toggle Active/Inactive">
                                            <?php echo $row['status'] === 'Active' ? 'Deactivate' : 'Activate'; ?>
                                        </a>

                                        <!-- Permanent Delete with JS Confirmation -->
                                        <a href="delete_student.php?id=<?php echo $row['student_id']; ?>&action=delete" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete <?php echo addslashes($row['name']); ?> permanently?');">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 25px; color: #6b7280;">
                                    No student records found matching your criteria.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <p style="margin-top: 15px; font-size: 13px; color: #6b7280;">
                Showing <?php echo $result ? $result->num_rows : 0; ?> of <?php echo $totalRows; ?> matching student record(s). All operations use MySQLi Prepared Statements.
            </p>
            <?php if ($totalPages > 1): ?>
                <div class="pagination" style="display:flex; gap:8px; justify-content:center; align-items:center; margin-top:20px; flex-wrap:wrap;">
                    <?php if ($page > 1): ?>
                        <a class="btn btn-secondary" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Previous</a>
                    <?php endif; ?>
                    <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                    <?php if ($page < $totalPages): ?>
                        <a class="btn btn-secondary" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026-27 StudentHub Portal - Practical 11 Student CRUD Module</p>
        </div>
    </footer>

</body>
</html>
