<?php
 ob_start();
 session_start();
include_once 'classes.php';
$db = new Database();
$conn = $db->dbConnection();
$error = false;
$nameError ='';
$emailError='';
$passError='';
$errMSG='';
$rnameError ='';
$remailError='';
$rpassError='';
$rerrMSG='';
$username='';
$pass='';
$email='';
$user = new USER();
if(isset($_GET['ref'])){
$ref= $_GET['ref'];
$stm = oci_parse($conn,"select username,emailid from users where reference='$ref'");    
oci_execute($stm);
$rows = oci_fetch_array($stm,OCI_BOTH);
$runame = $rows['USERNAME'];
$remail = $rows['EMAILID'];
$_SESSION['remail'] =$remail;
$_SESSION['runame'] =$runame;    
$stmt = $user->runQuery("select count(*) from users");
oci_execute($stmt);
$row = oci_fetch_array($stmt);
$users = $row['COUNT(*)'];
$stmt = $user->runQuery("select count(*) from topic");
oci_execute($stmt);
$row = oci_fetch_array($stmt);
$topic = $row['COUNT(*)'];
$stmt = $user->runQuery("select count(*) from activity");
oci_execute($stmt);
$row = oci_fetch_array($stmt);
$activities = $row['COUNT(*)'];
}
if( isset($_SESSION['user'])!="" ){
     // * Direct to home page here *
    header("Location: home.php");
 }else{
 if ( isset($_POST['register']) ) {  
  // clean user inputs to prevent sql injections
    $remail=$_SESSION['remail'];
    $runame=$_SESSION['runame'];

  $username = trim($_POST['username']);
  $username = strip_tags($username);
  $username = htmlspecialchars($username);
  
  $email = trim($_POST['email']);
  $email = strip_tags($email);
  $email = htmlspecialchars($email);
  
  $pass = trim($_POST['password']);
  $pass = strip_tags($pass);
  $pass = htmlspecialchars($pass);
  
//*Update User points whose username is $_POST['ref'];     
     
     
  // basic name validation
  if (empty($username)) {
   $error = true;
   $rnameError = "Please enter username."; 
  } else if (strlen($username) < 3) {
   $error = true;
   $rnameError = "Username must have atleat 3 characters.";
  } else{
    $oracle = oci_parse($conn, "select usersid from users where username='$username'");
    oci_execute($oracle);
      if(($row = oci_fetch_array($oracle, OCI_BOTH))!=false){
          $error=true;
       $rnameError = "Username not available";
   }
  }
  //basic email validation
  if ( !filter_var($email,FILTER_VALIDATE_EMAIL) ) {
   $error = true;
   $remailError = "Please enter valid email address.";
  } else {
   $oracle = oci_parse($conn, "select emailid from users where emailid='$email'");
    oci_execute($oracle);
   if(($row = oci_fetch_array($oracle, OCI_BOTH))!=false){
       $error=true;
       $emailError = "Email not available";
   }
  }
  if (empty($pass)){
   $error = true;
   $rpassError = "Please enter password.";
  } else if(strlen($pass) < 6) {
   $error = true;
   $rpassError = "Password must have atleast 6 characters.";
  }
  
  $password = md5($pass);
  
  if( !$error ) {
    $res = $user->register($username,$email,$password,$conn);
    $stmt = oci_parse($conn,"select usersid,username from users where username='$username'");
    oci_execute($stmt);
    $row = oci_fetch_array($stmt);
    $id = $row['USERSID'];  
    $desc =$username." Registered on E-Discussion refered by ".$runame;
    $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$id,sysdate,'$desc')");
    oci_execute($activity); 
   if ($res) {
    $errTyp = "success";
    $rerrMSG = "Successfully registered";
    $id = $user->ID();
       $code = md5($id);
   $message = "     
      Hello $username,
      <br /><br />
      Welcome to E-Discussion!<br/>
      To complete your registration  please , just click following <a href='http://localhost:84/e-discussion/verify.php?id=$id'>link </a><br/>
      <br /><br />        
      Use this verification code:
      $code
      <br>Thanks.";
      
   $subject = "Confirm Registration";
      
    $user->send_mail($email,$message,$subject); 
    $message = "     
      Hello $runame,
      <br /><br />
      Thank You!<br/>
      $username just created his/her account on E-Discussion using your reference<br/>
      <br /><br />";
      
   $subject = "Thank You!!!";
    $user->send_mail($remail,$message,$subject); 
       $stmt = oci_parse($conn,"select usersid,username from users where username='$username'");
       oci_execute($stmt);
       $row = oci_fetch_array($stmt);
    $user->login($row,$conn);
    $desc =$username." logged in";
    $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$id,sysdate,'$desc')");
    oci_execute($activity); 
   
       header("Location: editprofile.php?id=$id");
   } else {
    $errTyp = "danger";
    $rerrMSG = "Something went wrong, try again later..."; 
   } 
    
  }
 }
 }
?>
<!DOCTYPE html>
<html>
  <head>
    <title>E-Discussions</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700" rel="stylesheet"> 
        <link href="css/style.css" rel="stylesheet" media="screen">
        
        
        <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="js/jquery.leanModal.min.js"></script>
<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" />
<link type="text/css" rel="stylesheet" href="css/style1.css" />
  </head>
  <body>
    <div class="container-fluid">
      <div class="row">
  <div class="header ">
    
          <div class="container"> 
              <div class="col-md-12" > 
               <div id="box"> 
                 <h2 > Welcome to </h2> 
                 <h1> E-Disscussion</h1>
          				
                </div>
                
                <div class="mybutton">
                	<a id="modal_trigger" href="#modal" class="btn btn-warning bb">Sign in</a>
                	
					<a id="modal_trigger2" href="#modal_register" class="btn btn-warning bb2">Sign up</a>
			</div>
                  
              </div>
          </div>
      </div>

  
  
  <div class="container"> 
   <div class=" mid "> 
         <div class="Title_text"> 
           <h2> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, </h2>
          
            <p>  has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "</p>          
 
             </div>
     </div>
   </div>
   
     <h2 class="MileStonehead"> Milestone we have Archived  </h2> 
     <div class="MileStone">
     
      <div class="row">
      <div  class="container">
      
     
      <div class="row"> 
        <div class="col-md-4">
        <div class="circle "> 
         <p class="Mval"><?php echo $users ?> </p>
         <p class="Mitem">Users  </p>
         
         </div>
          </div>
          
        <div class="col-md-4">
        <div class="circle "> 
         <p class="Mval"><?php echo $topic ?></p>
         <p class="Mitem">Groups </p>
         
         </div>
          </div>
          
        <div class="col-md-4">
        <div class="circle "> 
         <p class="Mval"><?php echo $activities ?></p>
         <p class="Mitem">Topics  </p>
         
         </div>
          </div>
     
      </div>
    
     </div>
     
     </div>
</div>

<div class="footer">

</div>
  </div>
</div>




<div class="container">


	<div id="modal" class="popupContainer" style="display:none;">
		<header class="popupHeader">
			<span class="header_title">Sign in</span>
			<span class="modal_close"><i class="fa fa-times"></i></span>
		</header>
		
		<section class="popupBody">
			<!-- Social Login -->
			<div class="user_login">
        <form action="Register.php" method="POST" autocomplete="off">
                <label for="username">Username:</label>
                <input type="text" id="short" name="username" placeholder="Enter Username" value="<?php echo $username ?>">
            <span class="text-danger"><?php echo $emailError; ?></span>        
            <br>
                <label for= "password">Password:</label>
                <input type="password" id="short" name="password" placeholder="Enter Password" value="<?php echo $pass ?>">
            <span class="text-danger"><?php echo $passError; ?></span>        
            <br>
                <button type="submit" name="login"  class="register-button">Login</button>
        </form>
            <span class="text-danger"><?php echo $errMSG; ?></span>    
				<a href="forgerpass.php" class="forgot_password">Forgot password?</a>
			</div>
        </section>
</div>
</div>
<div class="container">

		<div id="modal_register" class="popupContainer" style="display:none;">
		<header class="popupHeader">
			<span class="header_title">SIGN UP</span>
			<span class="modal_close"><i class="fa fa-times"></i></span>
		</header>
		<section class="popupBody">
		
			<!-- Register Form -->
			<div class="user_register">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method="POST" autocomplete="off">
            <label for="username">Username:</label>
            <input type="text" id="short" name="username" placeholder="username" value="<?php echo $username ?>">
            <span class="text-danger"><?php echo $rnameError; ?></span>
            <br>
            <label for= "password">Password:</label>
            <input type="password" id="short" name="password" placeholder="password" value="<?php echo $pass ?>">
            <span class="text-danger"><?php echo $rpassError; ?></span>
            <br>
            <label for = "email">Email:</label>
            <input type="text" id="short" name="email" placeholder="email" value="<?php echo $email ?>">
            <span class="text-danger"><?php echo $remailError; ?></span>
            <br>
            <label for = "ref">Ref: </label>
            <input type="text" id="short" name="ref" placeholder="reference" value="<?php echo $_SESSION['runame']; ?>" disabled>
            <button type="submit" name="register"  class="register-button">Register</button>
        <span class="text-danger"><?php echo $rerrMSG; ?></span>
            
        </form>
			</div>
		</section>
	</div>
</div>
<?php 
    if($error && isset($_POST['login']))
    {  
      echo "<script type='text/javascript'>
	$('#modal_trigger').leanModal({top : 200, overlay : 0.6, closeButton: '.modal_close' });
    $('#modal_trigger2').leanModal({top : 75, overlay : 0.6, closeButton: '.modal_close' });
    var modal_height=$(\"#modal \").outerHeight();
    var modal_width=$(\"#modal \").outerWidth();
            $('#modal').css({'display':'block','position':'fixed','opacity':0,'z-index':11000,'left':50+'%','margin-left': - (modal_width/2)+'px','top':200+'px'});
            $('#modal').fadeTo(200,1);";
            
    echo 'var overlay=$("<div id=\'lean_overlay \'></div>");';
    echo '$("body").append(overlay);
    $("#lean_overlay").click(function(){
                        close_modal("#modal")});
                    $(".modal_close").click(function(){
                        close_modal("#modal")});
                
                
                $("#lean_overlay").css({"display":"block",opacity:0});
                    $("#lean_overlay").fadeTo(200,0.6);
                    
                function close_modal(modal_id){
                $("#lean_overlay").fadeOut(200);
                $(modal_id).css({"display":"none"});
                }
                                                      
    </script>    
    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script> ';
        
    }
      else if($error && (isset($_POST['register']))){
      echo "<script type='text/javascript'>
	$('#modal_trigger').leanModal({top : 200, overlay : 0.6, closeButton: '.modal_close' });
    $('#modal_trigger2').leanModal({top : 75, overlay : 0.6, closeButton: '.modal_close' });
    var modal_height=$(\"#modal_register \").outerHeight();
    var modal_width=$(\"#modal_register \").outerWidth();
            $('#modal_register').css({'display':'block','position':'fixed','opacity':0,'z-index':11000,'left':50+'%','margin-left': - (modal_width/2)+'px','top':75+'px'});
            $('#modal_register').fadeTo(200,1);";
            
    echo 'var overlay=$("<div id=\'lean_overlay \'></div>");';
    echo '$("body").append(overlay);
    $("#lean_overlay").click(function(){
                        close_modal("#modal_register")});
                    $(".modal_close").click(function(){
                        close_modal("#modal_register")});
                
                
                $("#lean_overlay").css({"display":"block",opacity:0});
                    $("#lean_overlay").fadeTo(200,0.6);
                    
                function close_modal(modal_id){
                $("#lean_overlay").fadeOut(200);
                $(modal_id).css({"display":"none"});
                }
                                                      
    </script>    
    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script> ';
          
      }
      else if(isset($_GET['ref'])){
      echo "<script type='text/javascript'>
	$('#modal_trigger').leanModal({top : 200, overlay : 0.6, closeButton: '.modal_close' });
    $('#modal_trigger2').leanModal({top : 75, overlay : 0.6, closeButton: '.modal_close' });
    var modal_height=$(\"#modal_register \").outerHeight();
    var modal_width=$(\"#modal_register \").outerWidth();
            $('#modal_register').css({'display':'block','position':'fixed','opacity':0,'z-index':11000,'left':50+'%','margin-left': - (modal_width/2)+'px','top':75+'px'});
            $('#modal_register').fadeTo(200,1);";
            
    echo 'var overlay=$("<div id=\'lean_overlay \'></div>");';
    echo '$("body").append(overlay);
    $("#lean_overlay").click(function(){
                        close_modal("#modal_register")});
                    $(".modal_close").click(function(){
                        close_modal("#modal_register")});
                
                
                $("#lean_overlay").css({"display":"block",opacity:0});
                    $("#lean_overlay").fadeTo(200,0.6);
                    
                function close_modal(modal_id){
                $("#lean_overlay").fadeOut(200);
                $(modal_id).css({"display":"none"});
                }
                                                      
    </script>    
    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script> ';
          
      }
      else{
echo '<script type="text/javascript">
	$("#modal_trigger").leanModal({top : 200, overlay : 0.6, closeButton: ".modal_close" });
    $("#modal_trigger2").leanModal({top : 75, overlay : 0.6, closeButton: ".modal_close" });
</script>
 <script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>';
      }
        ?>

  </body>
</html>
<?php ob_end_flush(); ?>