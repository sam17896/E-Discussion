<?php
require_once 'classes.php';
session_start();
$user = new USER();
$z=0;
$not=0;
$gr=0;
if(empty($_GET['id']) && empty($_GET['code']))
{
 $user->redirect('home.php');
}
if(isset($_GET['id']) && isset($_GET['code']))
{
 $id = $_GET['id'];
 $code = $_GET['code'];
 $stmt = $user->runQuery("SELECT username FROM users WHERE usersid=$id");
    pg_fetch_array($stmt);
    $row = pg_fetch_array($stmt,//oci_BOTH);
    $username = $row['USERNAME'];
    $msg = "
        <label for='pass' class='labels'>New Password: </label>    
        <input id='pass1' type='password' placeholder='New Password' name='pass' required />
        <label for='confirm-pass' class='labels'>Confirm Password: </label>
        <input id='pass2' type='password' placeholder='Confirm New Password' name='confirm-pass' required />
        <input type='submit' name='btn-reset-pass' value ='Reset'/>
        ";
}
  if(isset($_POST['btn-reset-pass']))
  {
      $db = new Database();
      $conn = $db->dbConnection();
    $msg = '';  
   $pass = $_POST['pass'];
   $cpass = $_POST['confirm-pass'];
   
   if($cpass!==$pass)
   {
    $msg = "<div class='alert alert-block'>
      <button class='close' data-dismiss='alert'>&times;</button>
      <strong>Sorry!</strong>  Password Doesn't match. 
      </div>";
   }
   else
   {
       $pass = md5($pass);
    $stmt = $user->runQuery("UPDATE users SET pass='$pass' WHERE usersid='$id'");
    pg_fetch_array($stmt);
       $stmt=$user->runQuery("select username from users where usersid=$id");
       pg_fetch_array($stmt);
       $row = pg_fetch_array($stmt);
       $username=$row['USERNAME'];
       $desc =$username." Password was reset";
    $activity = pg_query($conn,"insert into activity values(act_seq.nextval,$id,sysdate,'$desc')");
    pg_fetch_array($activity); 
    $msg = "<div class='alert alert-success'>
      <button class='close' data-dismiss='alert'>&times;</button>
      Password Changed.
      <a href='index.php'> Login </a>
      </div>";
    $user->login($row,$user->con);
    $desc =$username." Logged in";
    $activity = pg_query($conn,"insert into activity values(act_seq.nextval,$id,sysdate,'$desc')");
    pg_fetch_array($activity);
    $user->redirect('home.php');
   }
  } 
?>
<!DOCTYPE HTML>
<html>
<head>
        <title>Reset Password</title>
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
        <h2>Reset Password?</h2>
        <hr />
        <p><strong>Enter your new password to reset!</strong></p>
        <form class="resetss" method="post">
        <?php
        if(isset($msg))
          {
           echo $msg;
          }
          ?>
      </form>

    </div>
    </div>
</main>
<footer style="margin-top: 13%; height: 50px;">
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