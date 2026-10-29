<?php
require_once __DIR__."/db.php";require_once __DIR__."/functions.php";admin_only();
$event=["title"=>"","description"=>"","event_date"=>date("Y-m-d"),"venue"=>"","category"=>"Workshop","status"=>"Open","poster"=>null];$errors=[];
if($_SERVER["REQUEST_METHOD"]==="POST"){check_csrf();foreach(["title","description","event_date","venue","category","status"] as $k)$event[$k]=trim((string)($_POST[$k]??""));
if($event["title"]===""||mb_strlen($event["title"])>150)$errors[]="Title is required (maximum 150 characters).";
if($event["description"]==="")$errors[]="Description is required.";
$d=DateTime::createFromFormat("Y-m-d",$event["event_date"]);if(!$d||$d->format("Y-m-d")!==$event["event_date"])$errors[]="Valid event date is required.";
if($event["venue"]===""||mb_strlen($event["venue"])>180)$errors[]="Venue is required (maximum 180 characters).";
if(!in_array($event["category"],["Workshop","Seminar","Meeting","Exhibition","Competition","Cultural","Sports","General"],true))$errors[]="Invalid category.";
if(!in_array($event["status"],["Open","Closed"],true))$errors[]="Invalid status.";
if(!$errors){try{$poster=poster_upload($_FILES["poster"]??[],null);$s=$conn->prepare("INSERT INTO events(title,description,event_date,venue,category,status,poster) VALUES(?,?,?,?,?,?,?)");$s->bind_param("sssssss",$event["title"],$event["description"],$event["event_date"],$event["venue"],$event["category"],$event["status"],$poster);$s->execute();$id=(int)$s->insert_id;$s->close();audit($conn,(int)$_SESSION["admin_id"],"CREATE",$id,"Created event: ".$event["title"]);flash("success","Event created successfully.");go("index.php");}catch(Throwable $ex){$errors[]=$ex instanceof RuntimeException?$ex->getMessage():"Could not save event; check database setup.";}}}
?><!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Add Event</title><link rel="stylesheet" href="style.css"></head><body><?php include "top.php";?><main class="container narrow"><section class="card"><h2>Add New Event</h2><?php require "event_form.php";?></section></main><?php include "bottom.php";?></body></html>
