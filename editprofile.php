<?php
session_start();
require_once 'classes.php';
require_once 'DBConnect.php';
$db = new Database();
$conn = $db->dbConnection();
$topic1 = new topic();
$user = new USER();
$not=0;
$gr=0;
$z=0;
$Firstname='';
$Lastname='';
$gender='';
$Country='';
$phoneType='';
$mobileNumber='';
$Dp='';
$dob='';
$noti='';
if(!isset($_SESSION['user'])){
    header("location: index.php");
}
else{
    $username=$_SESSION['user'];
    $user->makeuser($conn,$username);
    $stmt = pg_query($conn,"select count(*) from messagenot where usersid = $user->userid and thread_id is not null");
     pg_fetch_array($stmt);
     $row=pg_fetch_array($stmt);
     $not=$row['COUNT(*)'];
     $stmt = pg_query($conn,"select count(*) from messagenot where usersid = $user->userid and topic_id is not null");
     pg_fetch_array($stmt);
     $row=pg_fetch_array($stmt);
     $gr = $row['COUNT(*)'];
     $stmt = pg_query($conn,"select count(*) from notification where usersid = $user->userid and status=0");
     pg_fetch_array($stmt);
     $row=pg_fetch_array($stmt);
     $z = $row['COUNT(*)'];
     $stmt = pg_query($conn,"select detail from notification where usersid=$user->userid order by time desc");
     pg_fetch_array($stmt);
     while($row=pg_fetch_array($stmt)){
         $noti .= "<p>".$row['DETAIL']."</p><hr>";
     }
    
}
if(empty($_GET['id'])){
if(isset($_POST['submit'] ) )
{ 
    if( $_FILES['userfile']['size'] > 0)
    {
	   $directory_self = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']); 
        $fileName = $_FILES['userfile']['name'];
        $tmpName  = $_FILES['userfile']['tmp_name'];
        $fileSize = $_FILES['userfile']['size'];
        $fileType = $_FILES['userfile']['type'];
        $fp      = fopen($tmpName, 'r');
        $content = fread($fp, filesize($tmpName));
        $content = addslashes($content);
        fclose($fp);
        $uploadsDirectory = $_SERVER['DOCUMENT_ROOT'] . $directory_self . 'uploaded_files/'; 
        $now = time(); 
        while(file_exists($uploadFilename = $uploadsDirectory.$now.'-'.$_FILES['userfile']['name'])) 
        { 
            $now++; 
        } 
        $databasename=$now.'-'.$_FILES['userfile']['name'];
        @move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFilename) ; 
    $sql = pg_query($conn , "UPDATE  users SET UserPic='$databasename'  WHERE Username = '$username' ");
    pg_fetch_array($sql);
    }
    if( $_FILES['coverfile']['size'] > 0)
    {
	   $directory_self = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']); 
        $fileName = $_FILES['coverfile']['name'];
        $tmpName  = $_FILES['coverfile']['tmp_name'];
        $fileSize = $_FILES['coverfile']['size'];
        $fileType = $_FILES['coverfile']['type'];
        $fp      = fopen($tmpName, 'r');
        $content = fread($fp, filesize($tmpName));
        $content = addslashes($content);
        fclose($fp);
        $uploadsDirectory = $_SERVER['DOCUMENT_ROOT'] . $directory_self . 'uploaded_files/'; 
        $now = time(); 
        while(file_exists($uploadFilename = $uploadsDirectory.$now.'-'.$_FILES['coverfile']['name'])) 
        { 
            $now++; 
        } 
        $databasename=$now.'-'.$_FILES['coverfile']['name'];
        @move_uploaded_file($_FILES['coverfile']['tmp_name'], $uploadFilename) ; 
    function error($error, $location, $seconds = 5) 
    { 
        header("Refresh: $seconds; URL='$location'"); 
        echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"'."n". 
        '"http://www.w3.org/TR/html4/strict.dtd">'."nn". 
        '<html lang="en">'."n". 
        '    <head>'."n". 
        '        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">'."nn". 
        '        <link rel="stylesheet" type="text/css" href="stylesheet.css">'."nn". 
        '    <title>Upload error</title>'."nn". 
        '    </head>'."nn". 
        '    <body>'."nn". 
        '    <div id="Upload">'."nn". 
        '        <h1>Upload failure</h1>'."nn". 
        '        <p>An error has occurred: '."nn". 
        '        <span class="red">' . $error . '...</span>'."nn". 
        '         The upload form is reloading</p>'."nn". 
        '     </div>'."nn". 
        '</html>'; 
        exit; 
    } // end error handler 
    $sql = pg_query($conn , "UPDATE  users SET Usercover='$databasename'  WHERE Username = '$username' ");
    pg_fetch_array($sql);
    }
	$details=new UserDetails();
    $gender=$_POST['gender'];
    $phoneType=$_POST['PhoneType'];
    $phonenumber=$_POST['number'];
    $dob=$_POST['DOB'];
    $interests=$_POST['interest'];
    $Firstname=ucfirst($_POST['FirstName']);
    $Lastname=ucfirst($_POST['lastname']);
    $skills=$_POST['skill'];
    $interests=$_POST['interest'];
    $Country=$_POST['country'];
    $institute=$_POST['institute'];
    $eduFrom=$_POST['from'];
    $eduTo=$_POST['To'];
    $company=$_POST['company'];
    $wotkTo=$_POST['workTo'];
    $wotkFrom=$_POST['workfrom'];
    $number = count($_POST['PhoneType']);
    for($i=0; $i<$number; $i++)  
    {  
        if(trim($_POST["PhoneType"][$i] != ''))  
        {
            $typeid = $_POST['PhoneType'][$i];
            if(trim($_POST["number"][$i] != '')){
                $num = $_POST["number"][$i];
                $details->insertNumber($conn,$typeid,$num,$user->userid);
            }
            
        }
     }
    $number = count($_POST['linktype']);
    for($i=0; $i<$number; $i++)  
    {  
        if(trim($_POST["linktype"][$i] != ''))  
        {
            $typeid = $_POST['linktype'][$i];
            if(trim($_POST["link"][$i] != '')){
                $lin = $_POST["link"][$i];
                $details->insertlink($conn,$typeid,$lin,$user->userid);
            }
            
        }
     }
    $number = count($_POST['interest']);
    for($i=0; $i<$number; $i++)  
    {  
        if(trim($_POST["interest"][$i] != ''))  
        {
            $in = $_POST['interest'][$i];
            $details->insertInterest($conn,$in,$user->userid);
        }
     }
    $number = count($_POST['skill']);
    for($i=0; $i<$number; $i++)  
    {  
        if(trim($_POST["skill"][$i] != ''))  
        {
            $sk = $_POST['skill'][$i];
            $details->insertSkill($conn,$sk,$user->userid);
        }
     }
    $number = count($_POST['institute']);
    for($i=0; $i<$number; $i++)  
    {  
        if(trim($_POST["institute"][$i] != '')&&trim($_POST["from"][$i] != '')&&trim($_POST["To"][$i] != ''))  
        {
            $ed = $_POST['institute'][$i];
            $fr =$_POST['from'][$i];
            $t  =$_POST['To'][$i];  
            $details->insertEducation($conn,$ed,$fr,$t,$user->userid);
        }
     }
    $number = count($_POST['company']);
    for($i=0; $i<$number; $i++)  
    {  
        if(trim($_POST["company"][$i] != '')&&trim($_POST["workfrom"][$i] != '')&&trim($_POST["workTo"][$i] != ''))  
        {
            $wk = $_POST['company'][$i];
            $wfr =$_POST['workfrom'][$i];
            $wt  =$_POST['workTo'][$i];  
            $details->insertWork($conn,$wk,$wfr,$wt,$user->userid);
        }
     }
    $details->saveDetails($conn,$user->userid,$Firstname,$Lastname,$gender,$Country,$dob);
    header("location: profile.php?id=$id");
}
else{
    header("location: home.php");
}
}
else{
    $id = $_GET['id'];
    if($id!=$user->userid){
    header("location: home.php");   
    }
    if (isset($_GET['edit'])){
$Firstname='';
$Lastname='';
$gender='';
$country='';
$phoneType='';
$mobileNumber='';
$Dp='';
$dob='';
$phone=''; 
$link='';   
$education='';    
$work='';
$skill='';
$interest='';
$cover=''; 
$stmt = pg_query($conn,"select userpic,usercover from users where usersid=$id");
pg_fetch_array($stmt);
$row = pg_fetch_array($stmt);
$Dp=$row['USERPIC'];
$cover = $row['USERCOVER'];    
$stmt = pg_query($conn,"select * from userdetails where usersid=$id");
pg_fetch_array($stmt);    
$row = pg_fetch_array($stmt);
$Firstname = $row['FIRST_NAME'];
$Lastname = $row['LAST_NAME'];
$gender = $row['GENDER'];
$country = $row['COUNTRY'];
$dob = $row['DOB'];
$stmt = pg_query($conn,"select p.phonenumber,n.typename,n.typeid,p.usersid from phonenumber p, numbertype n where p.usersid=$id and p.type_id = n.typeid");
pg_fetch_array($stmt);
while($row=pg_fetch_array($stmt)){
    $phone.= "<tr><td><h3>".$row['TYPENAME']."</h3></td>  
                        <td><h3>".$row['PHONENUMBER']."</h3></td>
                        
                        <td></td></tr>";  
}
$stmt = pg_query($conn,"select l.name,u.link from links l, userslinks u where u.usersid=$id and l.id = u.links_id");
pg_fetch_array($stmt);
while($row=pg_fetch_array($stmt)){            
    $link .= " <tr>  
                <td><h3>".$row['NAME']."</h3></td>  
                <td><a href=".$row['LINK']."><h3>".$row['LINK']."</h3></a></td>  
                <td></td>
               </tr>";
}
$stmt = pg_query($conn,"select * from education where usersid=$id");
pg_fetch_array($stmt);
while($row=pg_fetch_array($stmt)){            
    $education .= " <tr>  
                <td><h3>".$row['INSTITUTENAME']."</h3></td>  
                <td><h3>".$row['EFROM']."</h3></td>
                <td><h3>".$row['ETO']."</h3></td>
                <td></td>
               </tr>";
}
$stmt = pg_query($conn,"select * from work where usersid=$id");
pg_fetch_array($stmt);
while($row=pg_fetch_array($stmt)){            
    $work .= " <tr>  
                <td><h3>".$row['COMPANYNAME']."</h3></td>  
                <td><h3>".$row['WFROM']."</h3></td>
                <td><h3>".$row['WTO']."</h3></td>
                <td></td>
               </tr>";
}
$stmt = pg_query($conn,"select * from usersinterest where usersid=$id");
pg_fetch_array($stmt);
$interest.="<table class=\"table table-bordered\">";    
while($row=pg_fetch_array($stmt)){            
    $interest .= " <tr>  
                    <td><h3>".$row['INTREST']."</h3></td> 
                    <td></td>
                </tr>";
}
$stmt = pg_query($conn,"select * from usersskill where usersid=$id");
pg_fetch_array($stmt);
while($row=pg_fetch_array($stmt)){            
    $skill .= " <tr>  
                    <td><h3>".$row['SKILLNAME']."</h3></td>  
                    <td></td>
                </tr>";
}
}

    }
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Welcome - <?php echo $_SESSION['user']; ?></title>
<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css"  />
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/jquery-ui.css"/>     
    <link rel="stylesheet" href="css/font-awesome.css">
        <link rel="stylesheet" href="css/popup.css">
<link rel="stylesheet" href="css/bootstrap-theme.css">
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css"/>
<link rel="stylesheet" href="css/shashkay.css">    
<script src="js/jquery-1.11.0.min.js"></script>    
<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery-ui.js"></script>    
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
}    return false;    
});
});
</script>

<script type="text/javascript" >
$(document).ready(function()
{
    $("#notificationLink").click(function()
    {
        $.get("clear.php");
        $("#notificationContainer").fadeToggle(300);
        $("#notification_count").text("0");
        return false;
    });

    $(document).click(function()
    {
        $("#notificationContainer").hide();
    });

    //Popup on click
    $("#notificationContainer").click(function()
    {   
        return false;
    });

});
</script>
    
</head>
<body>
    <header>    
        <div class="container-fluid .head" style="background: #009688; border-bottom: 2px solid #00796B;">
            <div class="col-md-3">
                <h1 style="margin-top: 15px; color: #eee;">E-Discussion</h1>
            </div>
            <div class="col-md-5">
           <div><input type="text" class="form-control search"  id="searchid"  placeholder="Search" style="margin-top: 20px">
            <div class="dsply_frnd col-md-5 sc" id="result" style="display:none; z-index:1100; width:95%; max-height:200px; overflow-y:scroll;"></div>
            </div>
            </div>
            <div class="col-md-4 nav">
                <nav>
                        <ul style="margin-top: 27px; float: right;">
                            <a href="profile.php?id=<?php echo $user->userid; ?>"><i class="fa fa-user acc" aria-hidden="true" style=" margin-right: 15px; color: #eee; font-size: 20px;"></i></a>
                            <a href="home.php"><i class="fa fa-home" aria-hidden="true" style=" margin-right: 15px; color: #eee; font-size: 20px;"></i></a>
                            <a href="message.php?id=<?php echo $user->userid; ?>"><i class="fa fa-comments" aria-hidden="true" style=" margin-right: 15px; color: #eee; font-size: 20px;"><span class="badge"><?php echo $not ?></span></i></a>
                            <a id="notificationLink" href="#"><i class="fa fa-bell" aria-hidden="true" style=" margin-right: 15px; color: #eee; font-size: 20px;"><span id="notification_count" class="badge"><?php echo $z ?></span>
                            <div id="notificationContainer"><div id="notificationTitle" ><h3>Notifications</h3></div><div id="notificationsBody" class="sc"><?php echo $noti ?></div></div>
                            </i></a>
                            <a href="logout.php"><i class="fa fa-lock log" aria-hidden="true" style=" margin-right: 15px; color: #eee; font-size: 20px;"></i></a>
                        </ul>
                </nav>
            </div>
            <div class="sc col-md-5" id="result" style="display:none; max-height:200px; overflow-y:scroll;"></div>
        </div>    
    </header>
        <div class="ProfileSidebar col-md-12 " >
   
        <h2 style="color: #009688"> Personal Information </h2>
        <hr/ style="margin-top: 0px; border-color: #009688; border-width: thin;">        
           <div class="row">
         <div class="breakerLine"></div>
        <br>
         </div>
           <form method="post" enctype="multipart/form-data"  action="editprofile.php"> 
      <div class="form-group col-md-3"> 
      <label>Profile Picture:</label>
      
      <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
    <input id="userfile" type="file" name="userfile" />
    <img id="profileimg" src="uploaded_files/<?php echo $Dp ?>" style="height: 140px; width: 240px; border-radius: 8px; border: 2px solid #00796B; margin-top: 10px;" alt="profilepic"/> </div>
 	<div class="form-group col-md-9"> 
      <label>Cover Picture:</label>
      
      <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
    <input id="coverfile" type="file" name="coverfile" />
    <img id="profileimg" src="uploaded_files/<?php echo $cover ?>" style="height: 140px; width: 100%; border-radius: 8px; border: 2px solid #00796B; margin-top: 10px;" alt="coverpic"/> </div>
 	<div class="form-group  col-md-6">
       <label>First Name: </label>
						<input type="text" class="form-control" id="FirstName" name="FirstName" value= "<?php echo $Firstname ?>" placeholder="FirstName" required>
					</div>
                    	<div class="form-group  col-md-6">
       <label>Last Name: </label>
						<input type="text" class="form-control" id="LastName" name="lastname" value= "<?php echo $Lastname ?>"
						 placeholder="LastName" required>
					</div>
					
			<div class="form-group  col-md-6">
       <label>Date Of Birth: </label>
						<input type="text" class="form-control" id="DateOfBirth" name="DOB" value= "<?php echo $dob ?>"
						 placeholder="1/SEP/1995" required>
					</div>
					
<?php include_once 'includes/country.php' ?> 
            
                    
    <div class="form-group col-md-6">     
    <br /> 
   <label>Gender: </label>
   <br>
<label class="radio-inline"><input type="radio" name="gender" value="Male" checked>Male</label>
<label class="radio-inline"><input type="radio" name="gender" value="Female">Female</label> 
</div> 
         
         
<table class="table table-bordered" id="dynamic_field">
    <?php echo $phone; ?>
    <tr>  
<td> 
    <select  name="PhoneType[]"class="form-control "  >
     <?php $smtp=pg_query($conn,"Select typeid , typename from numbertype") ;
           pg_fetch_array($smtp);  
            while($row=pg_fetch_array($smtp)  )   {
                echo "<option value=\"".$row['TYPEID']. "\" > " .$row['TYPENAME'] ." </option>"; 
            }
     ?>
    </select>  
</td>  
     <td><input type="text" name="number[]" placeholder="Enter Number Here" class="form-control name_list" /></td>  
     <td><button type="button" name="add" id="add" class="btn btn-success">Add More</button></td>  
</tr>
    
</table>  
<table class="table table-bordered" id="link">  
    <?php echo $link; ?>
<tr>  
<td> 
    <select  name="linktype[]"class="form-control "  >
     <?php $smtp=pg_query($conn,"Select id , name from links") ;
           pg_fetch_array($smtp);  
            while($row=pg_fetch_array($smtp)  )   {
                echo "<option value=\"".$row['ID']. "\" > " .$row['NAME'] ." </option>"; 
            }
     ?>
    </select>  
    </td>  
     <td><input type="text" name="link[]" placeholder="www.facebook.com/username" class="form-control name_list" /></td>  
     <td><button type="button" name="add" id="addlink" class="btn btn-success">Add More</button></td>  
</tr>  
               </table>
<h3 style="color: #009688;">Educational Background:</h3>
<hr/ style="margin-top: 0px; border-color: #009688; border-width: thin;">        
<div class="breakerLine row"></div>
<br />
<table class="table table-bordered" id="dynamic_education">  
    <?php echo $education; ?>
    
    <tr>  
      <td class ="col-md-4"><label>Institute Name:</label><input type="text" name="institute[]" placeholder="Instittute Name " class="ed form-control name_list" /></td>  
    <td class ="col-md-3"> 	
     <label>From </label>	
    <select  name="from[]"class="form-control col-md-3"  >
                <?php include  'includes/year.php' ?>
             </select>  

        </td>  
        <td class ="col-md-3"> 	
         <label>To </label>	
    <select  name="To[]"class="form-control col-md-3"  >
                <?php include 'includes/year.php' ?>
             </select>  
        </td> 
         <td><br><button type="button" name="add" id="added" class="btn btn-success">Add More</button></td>  
    </tr>  
</table>

<h3 style="color: #009688;">Experience</h3>
<hr/ style="margin-top: 0px; border-color: #009688; border-width: thin;">                 
<div class="breakerLine row"></div>
<br />
<table class="table table-bordered" id="dynamic_work">  
    <?php echo $work; ?>
    <tr>  
      <td class ="col-md-4"><label>Company Name:</label><input type="text" name="company[]" placeholder="Company Name " class="ints form-control name_list" /></td>  
    <td class ="col-md-3"> 	
     <label>From </label>	
    <select  name="workfrom[]"class="form-control col-md-3"  >
                <?php include 'includes/year.php' ?>
             </select>  
        </td>  
        <td class ="col-md-3"> 	
         <label>To </label>	
    <select  name="workTo[]"class="form-control col-md-3"  >
                <?php include 'includes/year.php' ?>
             </select>  
        </td> 
         <td><br><button type="button" name="add" id="workadd" class="btn btn-success">Add More</button></td>  
    </tr>  
</table>
<h3 style="color:#009688;"> Techincal Information </h3>
<hr/ style="margin-top: 0px; border-color: #009688; border-width: thin;">                          
<div class="breakerLine row"></div>
<br />
<table class="table table-bordered" id="dynamic_Skills">  
    <?php echo $skill; ?>
    
<tr>  
<td class ="col-md-12">
    <label>Skill</label>
    <input type="text" name="skill[]" placeholder="C++ " class="skill form-control" /></td>  
 <td><br>
     <button type="button" name="add" id="skilladd" class="btn btn-success">Add More</button></td>  
</tr>  
</table>

<table class="table table-bordered" id="dynamic_interest">  
    <?php echo $interest ?>
<tr>  
<td class ="col-md-12">
    <label>Interest</label>
    <input type="text" name="interest[]" placeholder="Programming " class="interest form-control" /></td>  
 <td><br>
     <button type="button" name="add" id="Interestadd" class="btn btn-success">Add More</button>
</td>  
</tr>  
</table>
<button type="submit" name="submit"  class="register-button btn btn-warning loginBTN" style="width:100%; border-radius:0;" >Save</button>     
</form>    
</div>
<script>
var sk=[];
var inte=[];
var ed=[];
var ints=[]; 
$(document).ready(function(){
   skillupdated(); 
    interestupdated();
    educationupdated();
    instituteupdated();
});    
function skillupdated(){
    setTimeout(function(){
       updateskill();
       skillupdated();
    },2000);
}                  

function updateskill(){
    $.getJSON("skill.php",function(data){
      sk=[];
      var k=0;
        $.each(data.skill,function(){
            console.log(this['skill']);
            sk[k] = this['skill'];
            k++;
        });
        console.log(sk);
    });
      $( ".skill" ).autocomplete({
  source: sk
});
}
function interestupdated(){
    setTimeout(function(){
       updateinterest();
       interestupdated();
    },2000);
}                  

function updateinterest(){
    $.getJSON("interest.php",function(data){
      inte=[];
      var l=0;
        $.each(data.interest,function(){
            console.log(this['interest']);
            inte[l] = this['interest'];
            l++;
        });
        console.log(inte);
    });
      $( ".interest" ).autocomplete({
  source: inte
});
}
function educationupdated(){
    setTimeout(function(){
       updateeducation();
       educationupdated();
    },2000);
}                  

function updateeducation(){
    $.getJSON("education.php",function(data){
      ed=[];
      var m=0;
        $.each(data.education,function(){
            console.log(this['education']);
            ed[m] = this['education'];
            m++;
        });
        console.log(ed);
    });
      $( ".ed" ).autocomplete({
  source: ed
});
}
function instituteupdated(){
    setTimeout(function(){
       updateinstitute();
       instituteupdated();
    },2000);
}                  

function updateinstitute(){
    $.getJSON("institute.php",function(data){
      ints=[];
      var n=0;
        $.each(data.institute,function(){
            console.log(this['institute']);
            ints[n] = this['institute'];
            n++;
        });
        console.log(ints);
    });
      $( ".ints" ).autocomplete({
  source: ints
});
}
    
    
function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#profileimg')
                    .attr('src', e.target.result)
                    .width(200)
                    .height(200);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<script>  
<?php 
    $smtp=pg_query($conn,"Select typeid , typename from numbertype");
    pg_fetch_array($smtp);  
echo"$(document).ready(function(){  
      var i=1;  
      $('#add').click(function(){  
           i++;  
           $('#dynamic_field').append('<tr id=\"row'+i+'\"><td>	<select  name=\"PhoneType[]\"class=\"form-control \"  >";
                                       
    while($row=pg_fetch_array($smtp)  )   {
        echo "<option value=\"".$row['TYPEID'] . " \" > " .$row['TYPENAME'] ." </option>" ; 
    }

      echo "</select>  </td> <td><input type=\"text\" name=\"number[]\" placeholder=\"Enter Number Here\" class=\"form-control name_list\" /></td>  <td><button type=\"button\" name=\"remove\" id=\"'+i+'\" class=\"btn btn-danger btn_remove\">X</button></td></tr>');  
      });  
      $(document).on('click', '.btn_remove', function(){  
           var button_id = $(this).attr(\"id\");   
           $('#row'+button_id+'').remove();  
      });    
 }); 
 ";
    $smtp=pg_query($conn,"Select id , name from links");
    pg_fetch_array($smtp);  
echo"$(document).ready(function(){  
      var n=701;  
      $('#addlink').click(function(){  
           n++;  
           $('#link').append('<tr id=\"row'+n+'\"><td>	<select  name=\"linktype[]\"class=\"form-control \"  >";
                                       
    while($row=pg_fetch_array($smtp)  )   {
        echo "<option value=\"".$row['ID'] . " \" > " .$row['NAME'] ." </option>" ; 
    }

      echo "</select>  </td> <td><input type=\"text\" name=\"link[]\" placeholder=\"www.facebook.com/username\" class=\"form-control name_list\" /></td>  <td><button type=\"button\" name=\"remove\" id=\"'+n+'\" class=\"btn btn-danger btn_remove\">X</button></td></tr>');  
      });      
 }); 
 ";
 echo "$(document).ready(function(){  
      var j=101;  
      $('#added').click(function(){  
           j++;  
           $('#dynamic_education').append('<tr id=\"row'+j+'\"><td class=\"col-md-4\"><label> Institute Name</label><input type=\"text\" name=\"institute[]\" placeholder=\"Institute Name\" class=\"ed form-control name_list\" /></td><td class =\"col-md-3\"><label>From </label><select  name=\"from[]\"class=\"form-control col-md-3\"  >";include'includes/year.php';echo"</select></td><td class =\"col-md-3\"><label>To </label> <select  name=\"To[]\"class=\"form-control col-md-3\"  >";include'includes/year.php';echo" </select></td><td></br><button type=\"button\" name=\"remove\" id=\"'+j+'\" class=\"btn btn-danger btn_remove\">X</button></td></tr>');  
      });  
 });";
 
 echo "$(document).ready(function(){  
      var k=201;  
      $('#workadd').click(function(){  
           k++;  
           $('#dynamic_work').append('<tr id=\"row'+k+'\"><td class=\"col-md-4\"><label> Comapny Name</label><input type=\"text\" name=\"company[]\" placeholder=\"Company name\" class=\"ints form-control name_list\" /></td><td class =\"col-md-3\"><label>From </label><select  name=\"workfrom[]\"class=\"form-control col-md-3\"  >";include'includes/year.php';echo"</select></td><td class =\"col-md-3\"><label>To </label> <select  name=\"workTo[]\"class=\"form-control col-md-3\"  >";include'includes/year.php';echo" </select></td><td></br><button type=\"button\" name=\"remove\" id=\"'+k+'\" class=\"btn btn-danger btn_remove\">X</button></td></tr>');  
      });      
 });  
 ";
  ?>
 
 $(document).ready(function(){  
      var l=301;  
      $('#skilladd').click(function(){  
           l++;  
           $('#dynamic_Skills').append('<tr id="row'+l+'"><td class =""><label>Skill</label><input type="text" name="skill[]" placeholder="C++ " class="form-control name_list skill" /> </td><td></br><button type="button" name="remove" id="'+l+'" class="btn btn-danger btn_remove">X</button></td></tr>');  
      });    
 });  
 
 
 
  $(document).ready(function(){  
      var m=401;  
      $('#Interestadd').click(function(){  
           m++;  
           $('#dynamic_interest').append('<tr id="row'+m+'"><td class =""><label>Interest</label><input type="text" name="interest[]" placeholder="Programing " class="interest form-control" /> </td><td></br><button type="button" name="remove" id="'+m+'" class="btn btn-danger btn_remove">X</button></td></tr>');  
      });    
 });  
 </script>      
</body>
</html>
    
