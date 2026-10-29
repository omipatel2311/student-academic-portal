<?php
require_once __DIR__."/db.php";require_once __DIR__."/functions.php";admin_only();if($_SERVER["REQUEST_METHOD"]!=="POST")go("index.php");check_csrf();$id=filter_input(INPUT_POST,"id",FILTER_VALIDATE_INT);if(!$id){flash("error","Invalid event ID.");go("index.php");}
$s=$conn->prepare("SELECT title,poster FROM events WHERE id=?");$s->bind_param("i",$id);$s->execute();$ev=$s->get_result()->fetch_assoc();$s->close();if(!$ev){flash("error","Event not found.");go("index.php");}
$s=$conn->prepare("DELETE FROM events WHERE id=?");$s->bind_param("i",$id);$s->execute();$s->close();remove_poster($ev["poster"]??null);audit($conn,(int)$_SESSION["admin_id"],"DELETE",$id,"Deleted event: ".$ev["title"]);flash("success","Event deleted successfully.");go("index.php");
