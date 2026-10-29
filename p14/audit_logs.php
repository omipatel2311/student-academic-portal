<?php
require_once __DIR__ . "/auth.php";
require_admin();
$filterAction = trim($_GET['action'] ?? '');
$filterUser = trim($_GET['username'] ?? '');
$sql = "SELECT id, user_id, username, role, action, details, ip_address, created_at FROM audit_logs";
$where = [];
$params = [];
$types = '';
if ($filterAction !== '') {
    $where[] = "action LIKE ?";
    $params[] = "%{$filterAction}%";
    $types .= "s";
}
if ($filterUser !== '') {
    $where[] = "username LIKE ?";
    $params[] = "%{$filterUser}%";
    $types .= "s";
}
if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY id DESC LIMIT 100";
$stmt = $conn->prepare($sql);
if ($stmt && count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}
if ($stmt) {
    $stmt->execute();
    $logsRes = $stmt->get_result();
} else {
    $logsRes = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Audit Logs - Practical 14</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="container header-row">
        <div>
            <h1>StudentHub Admin Portal</h1>
            <p>Security &amp; Administrative Audit Trail</p>
        </div>
        <div class="header-user-badge">
            <a href="index.php" class="btn btn-sm btn-outline-light">&larr; Back to Dashboard</a>
        </div>
    </div>
</header>
<?php render_dynamic_menu('audit_logs'); ?>
<main class="container">
    <section class="card section-card">
        <div class="section-header">
            <div>
                <h3>📜 Audit Log Records</h3>
                <p class="section-desc">Tracks authentication, record updates, status toggles, and sensitive events.</p>
            </div>
        </div>
        <form method="GET" action="audit_logs.php" class="filter-bar">
            <input type="text" name="action" placeholder="Filter by action (e.g. LOGIN, TOGGLE)..." value="<?=htmlspecialchars($filterAction)?>">
            <input type="text" name="username" placeholder="Filter by username..." value="<?=htmlspecialchars($filterUser)?>">
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <?php if ($filterAction || $filterUser): ?>
                <a href="audit_logs.php" class="btn btn-secondary btn-sm">Reset</a>
            <?php endif; ?>
        </form>
        <div class="table-responsive" style="margin-top: 18px;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 70px;">Log ID</th>
                        <th style="width: 170px;">Timestamp</th>
                        <th style="width: 130px;">User</th>
                        <th style="width: 90px;">Role</th>
                        <th style="width: 160px;">Action</th>
                        <th>Details</th>
                        <th style="width: 120px;">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($logsRes && $logsRes->num_rows > 0): ?>
                    <?php while ($log = $logsRes->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?=(int)$log['id']?></strong></td>
                        <td><?=date("Y-m-d H:i:s", strtotime($log['created_at']))?></td>
                        <td><strong><?=htmlspecialchars($log['username'])?></strong></td>
                        <td><span class="role-badge role-badge-<?=strtolower($log['role'])?>"><?=htmlspecialchars($log['role'])?></span></td>
                        <td><span class="audit-action"><?=htmlspecialchars($log['action'])?></span></td>
                        <td><?=htmlspecialchars($log['details'])?></td>
                        <td><code><?=htmlspecialchars($log['ip_address'])?></code></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="empty-state">No audit logs matching criteria found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<footer>
    &copy; 2026 StudentHub &bull; Practical 14: System Audit Logs
</footer>

</body>
</html>
