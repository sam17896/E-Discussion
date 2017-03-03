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
    header("location: index.php");
}
else{
    $username=$_SESSION['user'];
    $user->makeuser($conn,$username);
    $id=$_GET['id'];
    $thread->clearnot($conn,$id,$user->userid);
}
?>
