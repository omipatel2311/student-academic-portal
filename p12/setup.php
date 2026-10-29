<?php
require_once __DIR__."/db.php";
$conn->query("CREATE TABLE IF NOT EXISTS admins(id INT AUTO_INCREMENT PRIMARY KEY,username VARCHAR(80) NOT NULL UNIQUE,password_hash VARCHAR(255) NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$conn->query("CREATE TABLE IF NOT EXISTS events(id INT AUTO_INCREMENT PRIMARY KEY,title VARCHAR(150) NOT NULL,description TEXT NOT NULL,event_date DATE NOT NULL,venue VARCHAR(180) NOT NULL,category VARCHAR(80) NOT NULL DEFAULT 'General',status ENUM('Open','Closed') NOT NULL DEFAULT 'Open',poster VARCHAR(255) DEFAULT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
// Repair an existing events table from an earlier practical if it lacks the id column.
$cols=[]; $cr=$conn->query("SHOW COLUMNS FROM events"); while($cc=$cr->fetch_assoc()) $cols[]=$cc["Field"];
if (!in_array("id",$cols,true)) { $conn->query("ALTER TABLE events ADD COLUMN id INT NOT NULL AUTO_INCREMENT UNIQUE FIRST"); $cols[]="id"; }
// Add any columns required by this practical without deleting existing event data.
$needed=["title"=>"VARCHAR(150) NOT NULL DEFAULT ''","description"=>"TEXT NOT NULL","event_date"=>"DATE NOT NULL DEFAULT '2000-01-01'","venue"=>"VARCHAR(180) NOT NULL DEFAULT ''","category"=>"VARCHAR(80) NOT NULL DEFAULT 'General'","status"=>"ENUM('Open','Closed') NOT NULL DEFAULT 'Open'","poster"=>"VARCHAR(255) DEFAULT NULL","created_at"=>"TIMESTAMP DEFAULT CURRENT_TIMESTAMP","updated_at"=>"TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"];
foreach($needed as $col=>$def){ if(!in_array($col,$cols,true)) $conn->query("ALTER TABLE events ADD COLUMN `$col` $def"); }
$conn->query("CREATE TABLE IF NOT EXISTS event_audit_logs(id INT AUTO_INCREMENT PRIMARY KEY,admin_id INT NOT NULL,action VARCHAR(30) NOT NULL,event_id INT NOT NULL,details VARCHAR(255) NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,INDEX(event_id),INDEX(admin_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$u="admin"; $h=password_hash("admin123",PASSWORD_DEFAULT);
$s=$conn->prepare("INSERT INTO admins(username,password_hash) VALUES(?,?) ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash)"); $s->bind_param("ss",$u,$h); $s->execute(); $s->close();
$n=(int)$conn->query("SELECT COUNT(*) total FROM events")->fetch_assoc()["total"];
if (!$n) {
 $samples=[["Web Development Workshop","Hands-on web development workshop.",date("Y-m-d",strtotime("+7 days")),"Computer Lab 1","Workshop","Open"],["Project Exhibition","Students display semester projects.",date("Y-m-d",strtotime("+14 days")),"Main Hall","Exhibition","Open"],["Orientation Meet","Student orientation session.",date("Y-m-d",strtotime("-7 days")),"Seminar Room","Meeting","Closed"]];
 $s=$conn->prepare("INSERT INTO events(title,description,event_date,venue,category,status) VALUES(?,?,?,?,?,?)");
 foreach($samples as $r){$s->bind_param("ssssss",$r[0],$r[1],$r[2],$r[3],$r[4],$r[5]);$s->execute();}$s->close();
}
?><!doctype html><html><head><meta charset="utf-8"><title>P12 Setup</title><link rel="stylesheet" href="style.css"></head><body><main class="container narrow"><section class="card"><h1>Setup complete</h1><p>Database tables, admin login and sample events are ready.</p><p>Username: <b>admin</b> | Password: <b>admin123</b></p><p class="notice">After setup, delete setup.php.</p><a class="btn" href="login.php">Go to Login</a></section></main></body></html>
