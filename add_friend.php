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
    $id = $_GET['id'];
    $stmt = pg_query($conn,"select friend_seq.nextval from dual");
    pg_fetch_array($stmt);
    $row = pg_fetch_array($stmt);
    $fid=$row['NEXTVAL'];
    $friendship->addfriendship($conn,$fid,$user->userid,$id);
    header("location: profile.php?id=$id");
}
?>