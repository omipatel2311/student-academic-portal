<?php
require_once __DIR__."/db.php"; require_once __DIR__."/functions.php";
if (!empty($_SESSION["admin_id"])) go("index.php");
$error="";
if ($_SERVER["REQUEST_METHOD"]==="POST") {
 check_csrf(); $u=trim((string)($_POST["username"]??"")); $p=(string)($_POST["password"]??"");
 $s=$conn->prepare("SELECT id,username,password_hash FROM admins WHERE username=? LIMIT 1"); $s->bind_param("s",$u); $s->execute(); $a=$s->get_result()->fetch_assoc(); $s->close();
 if ($a && password_verify($p,$a["password_hash"])) { session_regenerate_id(true); $_SESSION["admin_id"]=(int)$a["id"]; $_SESSION["admin_username"]=$a["username"]; flash("success","Login successful."); go("index.php"); } else $error="Incorrect username or password.";
}
?><!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Login</title><link rel="stylesheet" href="style.css"></head><body><header class="hero"><div class="wrap"><h1>EventHub Admin</h1><p>Practical 12 · Event Management CRUD</p></div></header><main class="container narrow"><section class="card"><h2>Admin Login</h2><?php flashes(); ?><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><label>Username<input name="username" required autocomplete="username"></label><label>Password<input type="password" name="password" required autocomplete="current-password"></label><button class="btn">Login</button></form><p class="muted small">Demo: admin / admin123</p></section></main></body></html>
