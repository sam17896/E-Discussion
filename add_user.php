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
if(empty($_GET['id'])){
    header("location: home.php");
}
else{
    $id = $_GET['id'];
    if(!$topic1->isTopic($conn,$id)){
        echo 'The link you requested is not available';
    }
    else{
        $topic1->maketopic($conn,$id);
        if($topic1->adminid==$user->userid){
            if(isset($_GET['userid'])){
                $userid = $_GET['userid'];
                $topic1->adduser($conn,$id,$userid);
                if($topic1->isRequested($conn,$id,$userid)){
                    $topic1->updatePermission($conn,$id,$userid);
                }
                
            }
            header("location: topic.php?id=$id");
        }
    }
}
?>