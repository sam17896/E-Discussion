<?php
require_once 'classes.php';
session_start();
$user = new USER();
$db = new Database();
$conn = $db->dbConnection();
if(isset($_SESSION['user'])){
    $username = $_SESSION['user'];
}
else{
    header("Location: index.php");
}
$user->makeuser($conn,$username);
if(empty($_GET['id']))
{
 $user->redirect('index.php');
}
if(isset($_GET['id']))
{
 $id = $_GET['id'];
 $code = md5($id);
 $_SESSION['CODE'] = $code;
 $_SESSION['id'] = $id;    
 $stmt = $user->runQuery("SELECT status_2 FROM users WHERE usersid=$id");
 oci_execute($stmt);    
 if(($row = oci_fetch_array($stmt, OCI_BOTH))!=false){
     if($row['STATUS_2']==0){
         $msg = "
             <div id='mesg' >
            <p>Please enter the verfication code from mail: </p>
            <form class='verifyy' action='verify.php' method='POST' autocomplete='off'>
            <label class='lebels' for='code'>Code: </label>
            <input type='text' id='short' name='code' placeholder='Code'>
            <input id='btn' type = 'submit' name='verify' value='Verify' />
            </div>
            </form>
          "; 
     }
  else
  {
   $msg = "<div class='alert alert-error'>
       <strong>sorry !</strong>  Your Account is already Activated!!";
  }
 }
 else
 {
  $msg = "
         <div class='alert alert-error'>
      <strong>sorry !</strong>  No Account Found : <a href='index.php'>Signup here</a>
      </div>
      ";
 }
}
if(isset($_POST['code'])){
    $c = $_POST['code'];
    if ($c==$_SESSION['CODE']){
        $id = $_SESSION['id'];
        $stm = $user->runQuery("update users set status_2 = 1 where usersid=$id");
        $res = oci_execute($stm);
        if($res){
        $msg = "<div class='alert alert-error'>
       <strong>Success</strong>  Your Account is Now Activated!!<br>";
        }else{
            $msg = "<div class='alert alert-error'>
       <strong>Opps</strong>  Something went wrong please try again later";
        }
        $stm = $user->runQuery("select emailid,username from users where usersid=$id");
        oci_execute($stm);
        $row = oci_fetch_array($stm,OCI_BOTH);
        $mail = $row['EMAILID'];
        $uname = $row['USERNAME'];
        $u = new USER();
        $ref = md5($uname);
        $stm = $user->runQuery("update users set reference = '$ref' where usersid=$id");
        oci_execute($stm);
        $message = "Hello $uname<br>
        Your account was activated!<br>
        Invite your friends with this <a href='http://localhost:84/e-discussion/reference.php?ref=$ref'>link</a><br> 
        <br><br>
        Thanks";
        $subject = "Account Verified!";
        $user->send_mail($mail,$message,$subject);
        $desc =$uname." account verified";
        $db = new Database();
        $conn = $db->dbConnection();
        $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$id,sysdate,'$desc')");
        oci_execute($activity); 
        
        $user->login($row,$conn);
    //    header("Location: home.php");
    }
    else{
        $msg = "<div class='alert alert-error'>
       <strong>Opps </strong>  Wrong Code !!";
    }
}

?>
<!DOCTYPE HTML>
<html>
<head>
        <title>Verification</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/jquery-ui.css"/>
        <link rel="stylesheet" href="css/forgStyle.css"/>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery-1.11.0.min.js"></script>
        <script src="js/jquery-ui.js"></script>
        <link rel="stylesheet" href="css/font-awesome.min.css"/>
    <script type="text/javascript">
        $(function(){
            $(".search").keyup(function(e) 
            {
                var searchid = $(this).val();
                if(e.keyCode==8){
                if(searchid.length==0){
                $("#result").hide();
            }
            }else{    
                var dataString = 'search='+ searchid;
                $.ajax({
                type: "POST",
                url: "search.php",
                data: dataString,
                cache: false,
                success: function(html)
                {
                $("#result").html(html).show();
                }
            });
            }return false;    
        });
    });
    </script>
</head>
    
<body style="background: #efefef;">
<header>    
        <div class="container-fluid .head" style="background: #009688; border-bottom: 2px solid #00796B;">
            <div class="col-md-12">
                <h1 style="margin-top: 15px; color: #eee;">E-Discussion</h1>
            </div> 
        </div>
</header>
<main>
    <div class="container-fluid">
    <div class="top col-md-12">
        <h2>Verification!</h2>
        <hr />
        <?php if(isset($msg)) { echo $msg; } ?>      
    </div>
    </div>
</main>
<footer style="margin-top: 15%; height: 56px;">
    <div class="container" id="foot">
    <div class="cr col-md-5">
    <img src="imgs/copyright2.png"/>
    2016 E-Discussion. All Rights Reserved.
    </div>
    <div class="icons col-md-7">
        <a href="#"><img src="imgs/instagram.png"/></a>
        <a href="#"><img src="imgs/in.png"/></a>
        <a href="#"><img src="imgs/youtube-128.png"/></a>
        <a href="#"><img src="imgs/tw.png"/></a>
        <a href="#"><img class="fb" src="imgs/fb.png"/></a>
    </div>
    </div>
</footer>
</body>
</html>