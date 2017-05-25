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
else if(empty($_GET['delete'])){
    $id = $_GET['id'];
    $topic1->maketopic($conn,$id);
    if(!$topic1->isTopic($conn,$id)){
        echo 'The link you requested is not available';
    }
    else{
        $stmt = pg_query($conn,"insert into permission values($id,$user->userid,0)");
        pg_fetch_array($stmt);
        $desc =$username." Requested to join the topic ".$topic1->name;
        $activity = pg_query($conn,"insert into activity values(act_seq.nextval,$user->userid,sysdate,'$desc')");
        pg_fetch_array($activity);             
        $desc =$username." Requested to join the topic ".$topic1->name;
        $activity = pg_query($conn,"insert into groupactivity values(act_seq.nextval,$user->userid,$id,sysdate,'$desc')");
        pg_fetch_array($activity);
        $desc = $username. " requested to join the Topic ".$topic1->name;
//        $notification = pg_query($conn,"insert into notification values(not_seq.nextval,$topic1->adminid,'$desc',0,sysdate)");
//        pg_fetch_array($notification);
       header("location: topic.php?id=$id");
    }
    }else{
    $id = $_GET['id'];
    $topic1->maketopic($conn,$id);
    if(!$topic1->isTopic($conn,$id)){
        echo 'The link you requested is not available';
    }
    else{
        $stmt = pg_query($conn,"delete from permission where topic_id=$id and usersid=$user->userid");
        pg_fetch_array($stmt);
        $desc =$username." Cancelled the request to join the topic ".$topic1->name;
        $activity = pg_query($conn,"insert into activity values(act_seq.nextval,$user->userid,sysdate,'$desc')");
        pg_fetch_array($activity);             
        $desc =$username." Cancelled the request to join the topic ".$topic1->name;
        $activity = pg_query($conn,"insert into groupactivity values(act_seq.nextval,$user->userid,$id,sysdate,'$desc')");
        pg_fetch_array($activity);             
        header("location: topic.php?id=$id");
        
    }
}
?>