<?php
 ob_start();
 session_start();
 if( isset($_SESSION['user'])!="" ){
    header("Location: home.php");
 }
include_once 'classes.php';
$db = new Database();
$username = '';
$pass = '';
$email ='';
$conn = $db->dbConnection();
$error = false;
$rnameError ='';
$remailError='';
$rpassError='';
$emailError='';
$passError='';
$errMSG ='';
$rerrMSG='';
$user = new USER();
$stmt =  pg_query($conn,"select * from users");
$users = pg_num_rows($stmt);
$stmt =pg_query($conn,"select * from topic");
$topic = pg_num_rows($stmt);
$stmt = pg_query($conn,"select * from activity");
$activities = pg_num_rows($stmt);
 if ( isset($_POST['register']) ) {  
  // clean user inputs to prevent sql injections
  $username = trim($_POST['username']);
  $username = strip_tags($username);
  $username = htmlspecialchars($username);
  
  $email = trim($_POST['email']);
  $email = strip_tags($email);
  $email = htmlspecialchars($email);
  
  $pass = trim($_POST['password']);
  $pass = strip_tags($pass);
  $pass = htmlspecialchars($pass);
  
     
  if (empty($username)) {
   $error = true;
   $rnameError = "Please enter username."; 
  } else if (strlen($username) < 3) {
   $error = true;
   $rnameError = "Username must have atleat 3 characters.";
  } else{
    $oracle = pg_query($conn, "select usersid from users where username='$username'");
      if($oracle){
          while(($row = pg_fetch_array($oracle))){
              $error=true;
           $rnameError = "Username not available";
       }
      }
  }
  //basic email validation
  if ( !filter_var($email,FILTER_VALIDATE_EMAIL) ) {
   $error = true;
   $remailError = "Please enter valid email address.";
  } else {
   $oracle = pg_query($conn, "select emailid from users where emailid='$email'");
  pg_fetch_array($oracle);
   if(($row = pg_fetch_array($oracle))!=false){
       $error=true;
       $remailError = "Email not available";
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
   if ($res) {
    $errTyp = "success";
    $rerrMSG = "Successfully registered";
    $id = $user->ID();
       $code = md5($id);
   $message = "     
      Hello $username,
      <br /><br />
      Welcome to E-Discussion!<br/>
      To complete your registration  please , just click following <a href='http://localhost:84/e-discussion/verify.php?id=$id'>link</a><br/>
      <br /><br />
      <br>
      Use this verification code:
      $code
      <br>Thanks.";
      
   $subject = "Confirm Registration";
      $stmt = pg_query($conn,"select usersid,username from users where username='$username'");
       $row = pg_fetch_array($stmt);
       $id = $row['usersid'];
       echo $row['username'];
       $desc = $username." registered on E-Discussion";
       $activity = pg_query($conn,"insert into activity (id,usersid,times,detail) values(act_seq.nextval,$id,sysdate,'$desc')");
       //pg_fetch_array($activity);
       $user->send_mail($email,$message,$subject); 
       $user->login($row,$conn);
       $desc =$username." Logged in";
       $activity = pg_query($conn,"insert into activity (id,usersid,times,detail)(id,usersid,times,detail) values(act_seq.nextval,$id,sysdate,'$desc')");
       //pg_fetch_array($activity);
  //    header("Location: editprofile.php?id=$id");
   } else {
       $error = true;
    $errTyp = "danger";
    $rerrMSG = "Something went wrong, try again later..."; 
   }  
  }
 }
if( isset($_POST['login']) ) { 
  $repsonse = array("error" => false);
  // prevent sql injections/ clear user invalid inputs
  $username = trim($_POST['username']);
  $username = strip_tags($username);
  $username = htmlspecialchars($username);
  
  $pass = trim($_POST['password']);
  $pass = strip_tags($pass);
  $pass = htmlspecialchars($pass);
  // prevent sql injections / clear user invalid inputs
  
  if(empty($username)){
   $error = true;
   $emailError = "Please enter your username";
   $repsonse["error"] = $error;
      $repsonse["error_msg"] = $emailError;
  } 
  if(empty($pass)){
   $error = true;
   $passError = "Please enter your password.";
    $repsonse["error"] = $error;
    $repsonse["error_msg"] = $passError;
  }
  
  // if there's no error, continue to login
  if (!$error) {
   $password = md5($pass); // password hashing using md5
    $sql = pg_query ($conn,"select usersid,username,pass from users where username='$username' and pass = '$password'");
    //$res = pg_fetch_array($sql);
    if(($row = pg_fetch_array($sql)) != false){
        $id = $row['USERSID'];
       $desc =$username." Logged in";
       $activity = pg_query($conn,"insert into activity values(act_seq.nextval,$id,sysdate,'$desc')");
       //$res=pg_fetch_array($activity);
       $user->login($row,$conn);
        $repsonse["error"] = $error;
      header("Location: home.php");
    }
      else{
          $error = true;
          $errMSG = "Wrong Username or Password..";
          $repsonse["error"] = $error;
          $repsonse["error_msg"] = $errMSG;
      }
    }
    echo json_encode(array('error'=>$repsonse));
    
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
           <h2 style="text-align:center">About Our Project</h2>
            <p>          In the current era of science and technology the use of computer and internet is ascending at a high pace. Everything today is being virtualized with the advancement in web technologies and so are our social media and educational platforms. There are many social websites being used by millions of users in the world today such that Facebook, LinkedIn, Twitter etc. To focus on the local region, these social websites are not playing productive role in our life putting it beside the quick communication medium, we use them mostly just for the entertainment purposes. The motivation begins with the thought that we use Facebook aimlessly hours and hours that results in no productive work rather than wastage of time. This project aims on the efficient and productive use of internet by developing an educational discussion forum that allow users to query related to their educational problems and other users to respond to it.<br><br>

Today people spend their most of the time on social website specially Facebook which includes majority of the students. If students use educational discussion forums rather than social websites, it will enhance their knowledge and will also make their time worth spending. This project basically targets the students of undergraduate and beyond level where educational boundaries are so vast that none can reach to them therefore in this situation such a discussion forum turn out to be very helpful. We aim to provide students a platform where they can socially interact with other students of same interest and can get help from the experts or teachers on any subject of interest.
<br><br>
The proposed system will have login system. Users will have to login by providing their basic info. A user can also become someone's friend by sending them friend request and by accepting theirs. A user can post a particular question and create a topic; he/she can then add other users to the discussion of that topic and then a topic will be added to a particular category that it belongs to. A user can also search for a topic in a category and request to be added to the discussion. A discussion is private to the group but the topic is a public entity. A user can be a member of multiple topics at a time. There will be a newsfeed having topics related to the interest of the user that displays the recommended topics for the user. Users can discuss in a group or they can converse privately with other students/friends. Most active group’s feeds will feature on the home page beside newsfeed. The web will keep track of group activities and student activities and update all the other users on the web about the groups they are part of in an activities box also on home page.
<br><br>
The front design is simple and user friendly. There will be a newsfeed displaying normal discussion topics. The user can view their profile and update accordingly. In the context of education this prototype website will provide the students to search through different topics and get more knowledge about their field of interest. Students can ask questions and clear their confusion regarding anything, moreover student can also gain knowledge by participating in other discussion topics. Student can interact with experts on the web and get first-hand knowledge or help on any particular topic or subject.<br><br>
</p>          
 
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
         <p class="Mitem">Topics</p>
         
         </div>
          </div>
          
        <div class="col-md-4">
        <div class="circle "> 
         <p class="Mval"><?php echo $activities ?></p>
         <p class="Mitem">User Activities</p>
         
         </div>
          </div>
     
      </div>
    
     </div>
     
     </div>
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
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method="POST" autocomplete="off">
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
      else if($error && isset($_POST['register'])){
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