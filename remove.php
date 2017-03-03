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
                if($userid!=$topic1->adminid)
                $topic1->removeuser($conn,$id,$userid);
                else{
                    echo "Admin of the topic cannot be removed";
                }
            }
            header("location: topic.php?id=$id");
        }
    }
}
?>