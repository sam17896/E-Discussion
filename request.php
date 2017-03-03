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
        $stmt = oci_parse($conn,"insert into permission values($id,$user->userid,0)");
        oci_execute($stmt);
        $desc =$username." Requested to join the topic ".$topic1->name;
        $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$user->userid,sysdate,'$desc')");
        oci_execute($activity);             
        $desc =$username." Requested to join the topic ".$topic1->name;
        $activity = oci_parse($conn,"insert into groupactivity values(act_seq.nextval,$user->userid,$id,sysdate,'$desc')");
        oci_execute($activity);
        $desc = $username. " requested to join the Topic ".$topic1->name;
//        $notification = oci_parse($conn,"insert into notification values(not_seq.nextval,$topic1->adminid,'$desc',0,sysdate)");
//        oci_execute($notification);
       header("location: topic.php?id=$id");
    }
    }else{
    $id = $_GET['id'];
    $topic1->maketopic($conn,$id);
    if(!$topic1->isTopic($conn,$id)){
        echo 'The link you requested is not available';
    }
    else{
        $stmt = oci_parse($conn,"delete from permission where topic_id=$id and usersid=$user->userid");
        oci_execute($stmt);
        $desc =$username." Cancelled the request to join the topic ".$topic1->name;
        $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$user->userid,sysdate,'$desc')");
        oci_execute($activity);             
        $desc =$username." Cancelled the request to join the topic ".$topic1->name;
        $activity = oci_parse($conn,"insert into groupactivity values(act_seq.nextval,$user->userid,$id,sysdate,'$desc')");
        oci_execute($activity);             
        header("location: topic.php?id=$id");
        
    }
}
?>