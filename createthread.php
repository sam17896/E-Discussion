<?php
session_start();
require_once 'classes.php';
require_once 'DBConnect.php';
$db = new Database();
$conn = $db->dbConnection();
$thread = new thread();
$user = new USER();
if(!isset($_SESSION['user'])){
    header("location: index.php");
}
if(empty($_GET['id'])){
    header("location: home.php");
}
else{
    $id=$_GET['id'];
    $username=$_SESSION['user'];
    $user->makeuser($conn,$username);
    if(!$thread->isthread($conn,$user->userid,$id)){
        $thread->makethread($conn,$user->userid,$id);
    }
    header("location: message.php?id=$user->userid");
}
?>
