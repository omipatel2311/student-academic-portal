<?php
require_once __DIR__ . "/auth.php";
if (!empty($_SESSION['user_id'])) {
    if (($_SESSION['role'] ?? '') === 'admin') {
        header("Location: index.php");
        exit();
    } else {
        header("Location: student_portal.php");
        exit();
    }
}
$error = '';
$msg = $_GET['msg'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if ($username === '' || $password === '') {
        $error = "Please provide both username and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, email, password, role, status FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$user || !password_verify($password, $user['password'])) {
            $error = "Invalid credentials. Please verify your username and password.";
            log_audit($conn, 'FAILED_LOGIN', "Failed login attempt for username: {$username}");
        } elseif ($user['status'] !== 'active') {
            $error = "Your account is deactivated. Contact system administrator.";
            log_audit($conn, 'INACTIVE_LOGIN_BLOCKED', "Deactivated user attempted login: {$username}");
        } else {
            // Login successful
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();
            // Update last_login
            $conn->query("UPDATE users SET last_login = NOW() WHERE id = " . (int)$user['id']);
            log_audit($conn, 'USER_LOGIN', "Successful authentication for role: {$user['role']}");
            if ($user['role'] === 'admin') {
                header("Location: index.php");
            } else {
                header("Location: student_portal.php");
            }
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Practical 14 RBAC Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
<div class="login-wrapper">
    <div class="login-box card">
        <div class="login-brand">
            <h2>StudentHub Portal</h2>
            <p>Practical 14 &bull; Admin & Student Authentication</p>
        </div>
        <?php if ($msg === 'timeout'): ?>
            <div class="alert alert-warning">Your session expired due to inactivity. Please log in again.</div>
        <?php elseif ($msg === 'auth_required'): ?>
            <div class="alert alert-warning">Authentication is required to access that dashboard.</div>
        <?php elseif ($msg === 'logged_out'): ?>
            <div class="alert alert-success">You have been logged out securely.</div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
        <?php endif; ?>
        <form method="POST" action="login.php" class="login-form">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" placeholder="e.g. admin or student" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
        </form>
        <div class="credentials-hint">
            <strong>Default Demo Credentials:</strong>
            <div class="cred-row">
                <span>Admin:</span> <code>admin</code> / <code>admin123</code>
            </div>
            <div class="cred-row">
                <span>Student:</span> <code>student</code> / <code>student123</code>
            </div>
        </div>
    </div>
</div>
</body>
</html>
