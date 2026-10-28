<?php
if(session_status()!==PHP_SESSION_ACTIVE){session_start();}
require_once __DIR__."/db.php";
if($_SERVER["REQUEST_METHOD"]!=="POST"){header("Location: login.php");exit;}
$username=trim((string)($_POST["username"]??""));$password=(string)($_POST["password"]??"");
if($username===""||$password===""){header("Location: login.php?error=invalid");exit;}
$stmt=$conn->prepare("SELECT id, username, email, password, role, status FROM users WHERE username = ? OR email = ? LIMIT 1");
if(!$stmt){http_response_code(500);exit("Check the users table columns.");}
$stmt->bind_param("ss",$username,$username);$stmt->execute();$user=$stmt->get_result()->fetch_assoc();$stmt->close();
if(!$user||!password_verify($password,$user["password"])){header("Location: login.php?error=invalid");exit;}
if(strtolower((string)$user["status"])!=="active"){header("Location: login.php?error=inactive");exit;}
session_regenerate_id(true);$_SESSION["user_id"]=(int)$user["id"];$_SESSION["username"]=$user["username"];$_SESSION["role"]=$user["role"];$_SESSION["last_activity"]=time();
$cookiePath="/p10_final/";
if(isset($_POST["remember"])){
 $token=bin2hex(random_bytes(32));$expiry=date("Y-m-d H:i:s",time()+30*24*60*60);
 $save=$conn->prepare("UPDATE users SET remember_token=?, remember_expiry=? WHERE id=?");
 if(!$save){http_response_code(500);exit("Add remember_token and remember_expiry columns using README.txt.");}
 $save->bind_param("ssi",$token,$expiry,$user["id"]);$save->execute();$save->close();
 setcookie("remember_token",$token,["expires"=>time()+30*24*60*60,"path"=>$cookiePath,"httponly"=>true,"samesite"=>"Lax"]);
}else{
 $save=$conn->prepare("UPDATE users SET remember_token=NULL, remember_expiry=NULL WHERE id=?");if($save){$save->bind_param("i",$user["id"]);$save->execute();$save->close();}
 setcookie("remember_token","",["expires"=>time()-3600,"path"=>$cookiePath,"httponly"=>true,"samesite"=>"Lax"]);
}
if($user["role"]==="admin"){header("Location: dashboard_admin.php");exit;}if($user["role"]==="student"){header("Location: dashboard_student.php");exit;}
$_SESSION=[];session_destroy();header("Location: login.php?error=invalid");exit;
?>