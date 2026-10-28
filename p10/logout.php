<?php
if(session_status()!==PHP_SESSION_ACTIVE){session_start();}require_once __DIR__."/db.php";
if(!empty($_SESSION["user_id"])){$id=(int)$_SESSION["user_id"];$s=$conn->prepare("UPDATE users SET remember_token=NULL, remember_expiry=NULL WHERE id=?");if($s){$s->bind_param("i",$id);$s->execute();$s->close();}}
setcookie("remember_token","",["expires"=>time()-3600,"path"=>"/p10_final/","httponly"=>true,"samesite"=>"Lax"]);$_SESSION=[];
if(ini_get("session.use_cookies")){$p=session_get_cookie_params();setcookie(session_name(),"",["expires"=>time()-42000,"path"=>$p["path"]?: "/","domain"=>$p["domain"]??"","secure"=>(bool)($p["secure"]??false),"httponly"=>(bool)($p["httponly"]??true),"samesite"=>"Lax"]);}
session_destroy();header("Location: login.php?loggedout=1");exit;
?>