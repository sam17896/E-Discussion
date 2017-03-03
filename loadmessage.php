<?php
session_start();
require_once 'classes.php';
require_once 'DBConnect.php';
$db = new Database();
$conn = $db->dbConnection();
$topic1 = new topic();
$user = new USER();
if(!isset($_SESSION['user'])){
    header("location: index.php");
}
else{
    $username=$_SESSION['user'];
    $user->makeuser($conn,$username);
}
if(empty($_SESSION['topic'])){
    header("location: home.php");
}
else{
    $id = $_SESSION['topic'];
    $topic1->getMessages($conn,$id);
}
?>