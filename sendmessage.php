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
if(isset($_SESSION['topic'])){
    $id = $_SESSION['topic'];
if(!$topic1->isTopic($conn,$id)){
        echo 'The link you requested is not available';
}
    else{
        $topic1->maketopic($conn,$id);
        $userid = $user->userid;
        if(!empty($_GET['message'])){
        $message = $_GET['message'];
        $stmt = pg_query($conn,"select msg_seq.nextval from dual");
        pg_fetch_array($stmt);
        $row = pg_fetch_array($stmt);
        $msgid = $row['NEXTVAL'];    
        $user->addmessage($conn,$userid,$message,$msgid);
        $topic1->addmessage($conn,$id,$msgid);
        $topic1->addmsgnot($conn,$id,$user->userid);    
        }
        else{
            echo 'please write some message';
        }
    }
//            header("location: topic.php?id=$id");
}
?>