<?php
require_once __DIR__ . "/auth.php";
require_login();
$msg = $_GET['msg'] ?? '';
$username = $_SESSION['username'] ?? 'Student';
$role = $_SESSION['role'] ?? 'student';
$eventsRes = $conn->query("SELECT id, title, description, event_date, venue, category FROM events WHERE status = 'Open' ORDER BY event_date ASC LIMIT 4");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Practical 14</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="container header-row">
        <div>
            <h1>StudentHub Student Portal</h1>
            <p>Practical 14 &bull; Student View (Role-Based Dynamic Access)</p>
        </div>
        <div class="header-user-badge">
            <span class="user-greeting">Welcome, <strong><?=htmlspecialchars($username)?></strong></span>
            <a href="logout.php" class="btn btn-sm btn-outline-light">Logout</a>
        </div>
    </div>
</header>
<?php render_dynamic_menu('student_portal'); ?>
<main class="container">
    <?php if ($msg === 'forbidden'): ?>
        <div class="alert alert-danger">
            ⛔ Access Denied: You tried to access an Administrator-only page. As a student, your access is scoped to student features.
        </div>
    <?php endif; ?>
    <section class="card section-card" style="margin-top: 25px;">
        <div class="section-header">
            <div>
                <h3>👋 Hello, <?=htmlspecialchars($username)?>!</h3>
                <p class="section-desc">You are logged in with the <strong>Student</strong> role.</p>
            </div>
            <span class="role-badge role-badge-student" style="font-size:14px; padding:6px 14px;">STUDENT ROLE ACTIVE</span>
        </div>
        <div class="alert alert-info">
            <strong>Role-Based Access Control in Action:</strong> Notice how your navigation menu above dynamically displays student links instead of administrative controls. If you try to open the Admin Dashboard, RBAC blocks access and preserves data security.
        </div>
        <h4 style="margin: 20px 0 10px; color: var(--purple-dark);">Upcoming Campus Events Available to You</h4>
        <div class="kpi-grid" style="margin-top: 15px;">
        <?php if ($eventsRes && $eventsRes->num_rows > 0): ?>
            <?php while ($ev = $eventsRes->fetch_assoc()): ?>
            <div class="card" style="border-left: 4px solid var(--purple);">
                <span class="badge badge-active" style="float: right;"><?=htmlspecialchars($ev['category'])?></span>
                <h4 style="margin-bottom: 6px;"><?=htmlspecialchars($ev['title'])?></h4>
                <p style="font-size: 13px; color: var(--muted); margin-bottom: 8px;"><?=htmlspecialchars($ev['description'])?></p>
                <div style="font-size: 12px; color: var(--ink);">
                    📅 <?=date("M d, Y", strtotime($ev['event_date']))?> &bull; 📍 <?=htmlspecialchars($ev['venue'])?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="empty-state">No open events available currently.</p>
        <?php endif; ?>
        </div>
    </section>
</main>
<footer>
    &copy; 2026 StudentHub &bull; Practical 14: Student Portal
</footer>
</body>
</html>
