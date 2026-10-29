<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
function e($v): string { return htmlspecialchars((string)($v ?? ""), ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8"); }
function go(string $u): never { header("Location: ".$u); exit; }
function csrf(): string { if (empty($_SESSION["csrf"])) $_SESSION["csrf"] = bin2hex(random_bytes(32)); return $_SESSION["csrf"]; }
function check_csrf(): void { if (!isset($_POST["csrf"], $_SESSION["csrf"]) || !hash_equals($_SESSION["csrf"], (string)$_POST["csrf"])) { http_response_code(403); exit("Invalid request token. Go back and try again."); } }
function admin_only(): void { if (empty($_SESSION["admin_id"])) go("login.php"); }
function flash(string $type,string $msg): void { $_SESSION["flash"]=["type"=>$type,"msg"=>$msg]; }
function flashes(): void { if (!empty($_SESSION["flash"])) { $f=$_SESSION["flash"]; echo '<div class="alert '.e($f["type"]).'">'.e($f["msg"]).'</div>'; unset($_SESSION["flash"]); } }
function audit(mysqli $db,int $admin,string $action,int $id,string $details): void { $s=$db->prepare("INSERT INTO event_audit_logs(admin_id,action,event_id,details) VALUES(?,?,?,?)"); $s->bind_param("isis",$admin,$action,$id,$details); $s->execute(); $s->close(); }
function remove_poster(?string $name): void { if ($name && basename($name)===$name) { $p=__DIR__."/uploads/".$name; if (is_file($p)) @unlink($p); } }
function poster_upload(array $f, ?string $old=null): ?string {
 if (($f["error"]??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE) return $old;
 if (($f["error"]??UPLOAD_ERR_OK)!==UPLOAD_ERR_OK) throw new RuntimeException("Poster upload failed.");
 if (($f["size"]??0)>2*1024*1024) throw new RuntimeException("Poster must be 2 MB or smaller.");
 $tmp=(string)($f["tmp_name"]??"");
 if (!is_uploaded_file($tmp)) throw new RuntimeException("Invalid uploaded file.");
 $mime=(new finfo(FILEINFO_MIME_TYPE))->file($tmp);
 $types=["image/jpeg"=>"jpg","image/png"=>"png","image/webp"=>"webp"];
 if (!isset($types[$mime]) || @getimagesize($tmp)===false) throw new RuntimeException("Only valid JPG, PNG, or WEBP images are allowed.");
 $name=bin2hex(random_bytes(16)).".".$types[$mime];
 if (!move_uploaded_file($tmp,__DIR__."/uploads/".$name)) throw new RuntimeException("Could not save poster. Check uploads folder.");
 if ($old) remove_poster($old);
 return $name;
}
