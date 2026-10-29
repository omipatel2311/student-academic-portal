<?php
require_once __DIR__ . "/auth.php";
require_admin();
$targetId = (int)($_GET['id'] ?? 0);
$token = $_GET['token'] ?? '';
if (!verify_csrf_token($token)) {
    die("Security verification failed. Invalid CSRF token.");
}
if ($targetId <= 0) {
    header("Location: index.php?msg=invalid_id#user-management");
    exit();
}
if ($targetId === (int)$_SESSION['user_id']) {
    header("Location: index.php?msg=cannot_toggle_self#user-management");
    exit();
}
$stmt = $conn->prepare("SELECT username, status FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $targetId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$user) {
    header("Location: index.php?msg=user_not_found#user-management");
    exit();
}
$newStatus = ($user['status'] === 'active') ? 'inactive' : 'active';
$upStmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
$upStmt->bind_param("si", $newStatus, $targetId);
if ($upStmt->execute()) {
    $upStmt->close();
    log_audit($conn, 'USER_STATUS_TOGGLE', "Changed status of user '{$user['username']}' (#{$targetId}) to {$newStatus}.");
    header("Location: index.php?msg=status_updated#user-management");
} else {
    $upStmt->close();
    header("Location: index.php?msg=update_failed#user-management");
}
exit();
?>
