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
else{
    $username=$_SESSION['user'];
    $user->makeuser($conn,$username);
}
if(empty($_GET['id'])){
    header("location: home.php");
}
else{
        $id = $_GET['id'];
        $userid = $user->userid;
        if(!empty($_GET['message'])){
        $message = $_GET['message'];
        $stmt = pg_query($conn,"select msg_seq.nextval from dual");
        pg_fetch_array($stmt);
        $row = pg_fetch_array($stmt);
        $msgid = $row['NEXTVAL'];    
        $thread->addmessage($conn,$userid,$message,$msgid);
        $friendid=$thread->addmess($conn,$id,$msgid);
        $thread->updatethread($conn,$id,$message);
        $thread->addmsgnot($conn,$id,$friendid);
        }
        else{
            echo 'please write some message';
        }
    }
//            header("location: topic.php?id=$id");
?>