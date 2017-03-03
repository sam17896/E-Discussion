<?php
session_start();
require_once 'classes.php';
require_once 'DBConnect.php';
$db = new Database();
$conn = $db->dbConnection();
$user = new USER();
if(!isset($_SESSION['user'])){
    header("location: index.php");
}
else{
    $username=$_SESSION['user'];
    $user->makeuser($conn,$username);
    if(!empty($_GET['id'])){
        $id = $_GET['id'];
        if($id==$user->userid){
            echo "yes";
        }
        else{
            echo "no";
        }
    }
}
?>
