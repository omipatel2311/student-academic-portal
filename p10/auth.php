<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require_once __DIR__ . "/db.php";
$timeoutSeconds = 300;
if (!empty($_SESSION["user_id"])) {
    $lastActivity = (int)($_SESSION["last_activity"] ?? time());
    if (time() - $lastActivity > $timeoutSeconds) {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $p = session_get_cookie_params();
            setcookie(session_name(), "", ["expires"=>time()-42000, "path"=>$p["path"] ?: "/", "domain"=>$p["domain"] ?? "", "secure"=>(bool)($p["secure"] ?? false), "httponly"=>(bool)($p["httponly"] ?? true), "samesite"=>"Lax"]);
        }
        session_destroy();
        header("Location: login.php?error=timeout"); exit;
    }
    $_SESSION["last_activity"] = time();
}
?>