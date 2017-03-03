<?php
session_start();
require_once 'classes.php';
$user = new USER();
$z=0;
$not=0;
$gr=0;
$db = new Database();
$conn = $db->dbConnection();
if($user->is_logged_in()!="")
{
 $user->redirect('index.php');
}

if(isset($_POST['btn-submit']))
{
 $email = $_POST['txtemail'];
 $stmt = $user->runQuery("SELECT username,usersid from (select users.*,Row_number() over (order by usersid) FROM users WHERE emailid='$email' order by usersid desc) where rownum=1");
 oci_execute($stmt);
 if(($row = oci_fetch_array($stmt,OCI_BOTH))!=false){
 $id = $row['USERSID'];
 $username = $row['USERNAME'];
 $desc =$username." Requested Password Reset Link";
 $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$id,sysdate,'$desc')");
 oci_execute($activity); 
 $code = md5(uniqid(rand())); 
  $_SESSION['resetcode'] = $code;
  $message= "
       Hello , $username
       <br /><br />
       We got requested to reset your password, if you do this then just click the following link to reset your password, if not just ignore                   this email,
       <br /><br />
       Click Following Link To Reset Your Password 
       <br /><br />
       <a href='http://localhost:84/e-discussion/resetpass.php?id=$id&code=$code'>Click here to reset your Password</a>
       <br /><br />
       thank you :)
       ";
  $subject = "Password Reset";
  
  $user->send_mail($email,$message,$subject);
  
  $msg = "<div class='alert alert-success'>
     <button class='close' data-dismiss='alert'>&times;</button>
     We've sent an email to $email.
                    Please click on the password reset link in the email to generate new password. 
      </div>";
 }
 else
 {
  $msg = "<div class='alert alert-danger'>
     <button class='close' data-dismiss='alert'>&times;</button>
     <strong>Sorry!</strong>  this email not found. 
       </div>";
 }
}
?>
<!DOCTYPE HTML>
<html>
<head>
        <title>Forget Password</title>
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
            <h2>Forgot Your Password?</h2>
            <hr />
            <p><strong>Enter your e-mail address to receive a link to reset your password!</strong></p>
      <form class="forg" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>">
    <?php
   if(isset($msg))
   {
    echo $msg;
   }
   else
   {
    ?>
          <label for="txtemail" class="labels">E-mail: </label>
            <input type="email" id="mail" name="txtemail" placeholder="example@abcmail.com"/>
            <input type="submit" name="btn-submit" value="Submit"/>
    <?php
   }
   ?>
</form>
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
   </body>
</html>