<?php
require_once __DIR__."/db.php";
$accounts=[["admin","admin@example.com","admin123","admin"],["student","student@example.com","student123","student"]];
foreach($accounts as $a){[$username,$email,$plain,$role]=$a;$hash=password_hash($plain,PASSWORD_DEFAULT);
$f=$conn->prepare("SELECT id FROM users WHERE username=? LIMIT 1");$f->bind_param("s",$username);$f->execute();$old=$f->get_result()->fetch_assoc();$f->close();
if($old){$s=$conn->prepare("UPDATE users SET email=?,password=?,role=?,status='active' WHERE id=?");$s->bind_param("sssi",$email,$hash,$role,$old["id"]);}
else{$s=$conn->prepare("INSERT INTO users (username,email,password,role,status) VALUES (?,?,?,?,'active')");$s->bind_param("ssss",$username,$email,$hash,$role);}
if(!$s->execute()){exit("Setup failed. Check users table columns and unique email values.");}$s->close();}
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Setup Complete</title></head><body><h2>Admin and Student accounts created successfully!</h2><p>Admin: admin / admin123</p><p>Student: student / student123</p><p>Delete setup_users.php after setup.</p></body></html>