<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require_once __DIR__ . "/db.php";
if (!empty($_SESSION["user_id"])) { return; }
if (empty($_COOKIE["remember_token"])) { return; }
$token = (string)$_COOKIE["remember_token"];
$stmt = $conn->prepare("SELECT id, username, role, status FROM users WHERE remember_token = ? AND remember_expiry > NOW() LIMIT 1");
if (!$stmt) { return; }
$stmt->bind_param("s", $token);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$user || strtolower((string)$user["status"]) !== "active") {
    setcookie("remember_token", "", ["expires"=>time()-3600, "path"=>"/p10_final/", "httponly"=>true, "samesite"=>"Lax"]);
    return;
}
session_regenerate_id(true);
$_SESSION["user_id"] = (int)$user["id"];
$_SESSION["username"] = $user["username"];
$_SESSION["role"] = $user["role"];
$_SESSION["last_activity"] = time();
if ($user["role"] === "admin") { header("Location: dashboard_admin.php"); exit; }
if ($user["role"] === "student") { header("Location: dashboard_student.php"); exit; }
?>