<?php
session_start();
require_once 'classes.php';
require_once 'DBConnect.php';
$user = new USER();
$db = new Database();
$conn = $db->dbConnection();
if(!$user->is_logged_in())
{
 $user->redirect('index.php');
}

if($user->is_logged_in()!="")
{
    $username = $_SESSION['user'];
    $user->makeUser($conn,$username);
    $user->logout($conn); 
    $desc =$username." Logged out";
    $id = $user->userid;
    $activity = pg_query($conn,"insert into activity values(act_seq.nextval,$id,sysdate,'$desc')");
    pg_fetch_array($activity); 
   $user->redirect('index.php');
}
?>
