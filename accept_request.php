<?php
session_start();
require_once 'classes.php';
require_once 'DBConnect.php';
$db = new Database();
$conn = $db->dbConnection();
$friendship = new friendship();
$user = new USER();
if(!isset($_SESSION['user'])){
    header("location: index.php");
}
else{
    $username=$_SESSION['user'];
    $user->makeuser($conn,$username);
}
if(empty($_GET['id'])){
    header("location: home.php");
}
else{
    $fid = $_GET['id'];
    $id = $_GET['userid'];
    $friendship->acceptfriendship($conn,$fid,$user->userid,$id);
    header("location: profile.php?id=$id");
}
?>