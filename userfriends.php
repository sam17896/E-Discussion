<?php
session_start();
require_once 'classes.php';
require_once 'DBConnect.php';
$db = new Database();
$conn = $db->dbConnection();
$profile = new profile();
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
    $id = $_GET['id'];
    $profile->getFriends($conn,$id);
    //header("location: profile.php?id=$id");
}
?>