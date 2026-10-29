<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . "/db.php";
$timeout = 900;
if (!empty($_SESSION['user_id'])) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        log_audit($conn, 'SESSION_TIMEOUT', 'User session expired due to 15m inactivity.');
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
        }
        session_destroy();
        header("Location: login.php?msg=timeout");
        exit();
    }
    $_SESSION['last_activity'] = time();
}
function log_audit(mysqli $conn, string $action, string $details): void {
    $userId = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['username'] ?? 'guest';
    $role = $_SESSION['role'] ?? 'guest';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $stmt = $conn->prepare("INSERT INTO `audit_logs` (`user_id`, `username`, `role`, `action`, `details`, `ip_address`) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("isssss", $userId, $username, $role, $action, $details, $ip);
        $stmt->execute();
        $stmt->close();
    }
}
function require_admin(): void {
    if (empty($_SESSION['user_id'])) {
        header("Location: login.php?msg=auth_required");
        exit();
    }
    if (($_SESSION['role'] ?? '') !== 'admin') {
        header("Location: student_portal.php?msg=forbidden");
        exit();
    }
}
function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        header("Location: login.php?msg=auth_required");
        exit();
    }
}
function get_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function verify_csrf_token(?string $token): bool {
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
function render_dynamic_menu(string $currentPage = 'dashboard'): void {
    $isLoggedIn = !empty($_SESSION['user_id']);
    $role = $_SESSION['role'] ?? 'guest';
    $username = $_SESSION['username'] ?? '';
    echo '<nav class="sub-nav"><div class="container nav-wrap">';
    echo '<div class="nav-links">';
    if ($isLoggedIn && $role === 'admin') {
       
        echo '<a href="index.php" class="' . ($currentPage === 'dashboard' ? 'active' : '') . '">📊 Dashboard</a>';
        echo '<a href="#user-management" class="' . ($currentPage === 'users' ? 'active' : '') . '">👥 User Management</a>';
        echo '<a href="audit_logs.php" class="' . ($currentPage === 'audit_logs' ? 'active' : '') . '">📜 Audit Logs</a>';
        echo '<a href="#analytics" class="' . ($currentPage === 'analytics' ? 'active' : '') . '">📈 Analytics Chart</a>';
        echo '<a href="../p11/index.php" target="_blank">🎓 Student CRUD (P11)</a>';
        echo '<a href="../p12/index.php" target="_blank">📅 Event CRUD (P12)</a>';
        echo '<a href="../p13/index.php" target="_blank">⚡ Fetch API (P13)</a>';
    } elseif ($isLoggedIn && $role === 'student') {
        
        echo '<a href="student_portal.php" class="' . ($currentPage === 'student_portal' ? 'active' : '') . '">🎓 Student Portal</a>';
        echo '<a href="../p12/index.php" target="_blank">📅 View Campus Events</a>';
        echo '<a href="../p13/index.php" target="_blank">⚡ Student Directory</a>';
    } else {
        
        echo '<a href="login.php" class="' . ($currentPage === 'login' ? 'active' : '') . '">🔑 Login</a>';
        echo '<a href="../p10/index.php">Practical 10</a>';
    }
    echo '</div>';
    if ($isLoggedIn) {
        $badgeClass = $role === 'admin' ? 'role-badge-admin' : 'role-badge-student';
        echo '<div class="user-status-area">';
        echo '<span class="user-pill ' . $badgeClass . '">👤 ' . htmlspecialchars($username) . ' (' . ucfirst($role) . ')</span>';
        echo '<a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>';
        echo '</div>';
    } else {
        echo '<div class="user-status-area"><a href="login.php" class="btn btn-primary btn-sm">Sign In</a></div>';
    }
    echo '</div></nav>';
}
?>
