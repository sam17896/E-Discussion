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
$r='';
$accept='';
$reject='';
$cr='';
$ar='';
$rr='';
$m='';
$ep='';
$noti='';
$clas = 'col-md-9';
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
    header("location: home.php");
}
else{
    $id = $_GET['id'];
    if($user->userid==$id){
          $r = ' <activities id="act" class="col-md-3" style="margin-top: 15px;">
            <div class="col-md-12"><h3 style="text-align: center; color: #009688;">Activities</h3>
            <hr/ style="margin-top: 0px; border-color: #009688; border-width: thin;">
            </div>
            <div id="activity" class="sc" style="overflow-y: scroll;width: 100%;height: 500px; overflow-y:scroll;">
            </div>
            </activities>';
        $ep= "<a class=\"col-md-offset-6\" href=\"editprofile.php?id=$id&edit='true'\"><button>Edit Profile</button></a>";
}
else{
    $clas = 'col-md-12';
    $stmt = pg_query($conn,"select username from users where usersid=$id");
    pg_fetch_array($stmt);
    $row = pg_fetch_array($stmt);
    $username = $row['USERNAME'];
    $stmt = pg_query($conn,"select id from friendship where senderid=$id and recieverid=$user->userid and status_2=0");
    pg_fetch_array($stmt);
    if($row=pg_fetch_array($stmt)){
        $fid=$row['ID'];
        $accept="<a style='margin-right:10px' href='accept_request.php?id=$fid&userid=$id'><button>Accept Request</button></a>";
        $reject="<a style='margin-right:10px' href='reject_request.php?id=$fid&userid=$id'><button>Reject Request</button></a>";
    }else{
        $stmt = pg_query($conn,"select id from friendship where senderid=$user->userid and recieverid=$id and status_2=0");
        pg_fetch_array($stmt);
        if($row=pg_fetch_array($stmt)){
            $fid =$row['ID'];
            $cr= "<a style='margin-right:10px' href='reject_request.php?id=$fid&userid=$id'><button>Cancel Request</button></a>";
        }else{
            $stmt = pg_query($conn,"select friendship_id from usersfriend where usersid=$user->userid and friendid=$id");
            pg_fetch_array($stmt);
            if($row=pg_fetch_array($stmt)){
                $fid=$row['FRIENDSHIP_ID'];
                $rr="<a style='margin-right:10px' href='remove_friend.php?id=$fid&userid=$id'><button>Remove Friend</button></a>";
            }else{
                $ar= "<a style='margin-right:10px' href='add_friend.php?id=$id'><button>Add Friend</button></a>";
            }
        }
    }
    $m ="<a style='margin-right:10px' href='createthread.php?id=$id'><button> Send Message </button></a>";
}
$Firstname='';
$Lastname='';
$gender='';
$Country='';
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
$stmt = pg_query($conn,"select p.phonenumber,n.typename from phonenumber p, numbertype n where p.usersid=$id and p.type_id = n.typeid");
pg_fetch_array($stmt);
$phone.="<table class=\"table table-bordered\">";    
while($row=pg_fetch_array($stmt)){            
    $phone .= " <tr>  
                <td><h3>".$row['TYPENAME']."</h3></td>  
                <td><h3>".$row['PHONENUMBER']."</h3></td>  
               </tr>";
}
$phone.="</table>";    
$stmt = pg_query($conn,"select l.name,u.link from links l, userslinks u where u.usersid=$id and l.id = u.links_id");
pg_fetch_array($stmt);
$link.="<table class=\"table table-bordered\">";    
while($row=pg_fetch_array($stmt)){            
    $link .= " <tr>  
                <td><h3>".$row['NAME']."</h3></td>  
                <td><a href=".$row['LINK']."><h3>".$row['LINK']."</h3></a></td>  
               </tr>";
}
$link.="</table>";    
$stmt = pg_query($conn,"select * from education where usersid=$id");
pg_fetch_array($stmt);
$education.="<table class=\"table table-bordered\">";    
while($row=pg_fetch_array($stmt)){            
    $education .= " <tr>  
                <td><h3>".$row['INSTITUTENAME']."</h3></td>  
                <td><h3>".$row['EFROM']."-".$row['ETO']."</h3></td>  
               </tr>";
}
$education.="</table>";    
$stmt = pg_query($conn,"select * from work where usersid=$id");
pg_fetch_array($stmt);
$work.="<table class=\"table table-bordered\">";    
while($row=pg_fetch_array($stmt)){            
    $work .= " <tr>  
                <td><h3>".$row['COMPANYNAME']."</h3></td>  
                <td><h3>".$row['WFROM']."-".$row['WTO']."</h3></td>  
               </tr>";
}
$work.="</table>";    
$stmt = pg_query($conn,"select * from usersinterest where usersid=$id");
pg_fetch_array($stmt);
$interest.="<table class=\"table table-bordered\">";    
while($row=pg_fetch_array($stmt)){            
    $interest .= " <tr>  
                    <td><h3>".$row['INTREST']."</h3></td>  
                </tr>";
}
$interest.="</table>";    
$stmt = pg_query($conn,"select * from usersskill where usersid=$id");
pg_fetch_array($stmt);
$skill.="<table class=\"table table-bordered\">";    
while($row=pg_fetch_array($stmt)){            
    $skill .= " <tr>  
                    <td><h3>".$row['SKILLNAME']."</h3></td>  
                </tr>";
}
$skill .="</table>";    

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
<link rel="stylesheet" href="css/bootstrap-theme.css">
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<link rel="stylesheet" href="css/font-awesome.css"/>
<link rel="stylesheet" href="css/popup.css"/>        
<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css"/>
<link rel="stylesheet" href="css/shashkay.css">    
<script src="js/jquery-1.11.0.min.js"></script>    
<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
    $(document).ready(function(){
        var id = <?php echo $id ?>;
        $.get("yourprofile.php?id="+id,function(data){
            if(data=="no"){
                $("#act").hide();
            }
            else{
                $("#act").show();
            }
        });
        updateactivity();
        activityupdated();
        updatefriends();
        friendsupdated();
        updategroups();
        groupsupdated();
        function activityupdated(){
                setTimeout(function(){
                   updateactivity();
                   activityupdated();
                },1000);
            }                  
        function updateactivity(){
        $.getJSON("loadactivity.php",function(data){
          $("#activity").empty();
           $.each(data.activity,function(){
               $("#activity").append("<p>"+this['detail']+"</p><hr/>");
            });
       });
        }
        function friendsupdated(){
                setTimeout(function(){
                   updatefriends();
                   friendsupdated();
                },2000);
            }                  
        
        function updatefriends(){
            var uid = <?php echo $id; ?>;
            $.getJSON("userfriends.php?id="+uid,function(data){
                $("#friends").empty();
                $.each(data.friends,function(){
                        $("#friends").append("<a href='profile.php?id="+this['id']+"'><img src='uploaded_files/" +this['userpic']+"' style=\"width:25px; height:25px; margin-right:6px;\" /><span>"+this['name']+"<p></span><hr/>");
            });
       });
        }
        function groupsupdated(){
                setTimeout(function(){
                   updategroups();
                   groupsupdated();
                },2000);
            }                  
        
        function updategroups(){
            var uid = <?php echo $id; ?>;
            $.getJSON("usertopic.php?id="+uid,function(data){
                $("#groups").empty();
                $.each(data.topics,function(){
               $("#groups").append("<a href='topic.php?id="+this['id']+"'>"+this['name']+"</a><br><hr/>");
            });
       });
        }
       
    });
    
</script>
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
            <div class="dsply_frnd col-md-5" id="result" style="display:none; max-height:200px; overflow-y:scroll;"></div>
   
        </div>    
    </header>
        <sidebar class="col-md-3" style="width: 22%;">
       
            <div class="image" style="height: 200px; width: 70%; margin: auto; margin-top:5px;">
                <img class="userpic" src="uploaded_files/<?php echo $Dp ?>" style="border-radius: 5px; border:3px solid #009688; width: 100%; height: 100%;">
            </div>
            <h2 style="text-transform: none; color: #009688; text-align: center;font-weight: 400;font-size: 25px;"><?php echo $username ?></h2>
            <hr/ style="border-color: #009688; border-width: thin;">
       <div id='fri'>
            <h3 style="font-weight: 400;">Friends</h3>
           <hr/ style="border-color: #009688; border-width: thin;">
            <div id='friends' class="sc" style="overflow-y: scroll;width: 100%;height: 250px;">
            </div>
        </div>
       <div id='gr'>
            <h3 style="font-weight: 400;">Groups</h3>
           <hr/ style="border-color: #009688; border-width: thin;">
            <div id='groups' class="sc" style="overflow-y: scroll;width: 100%;height: 250px;">
            </div>
        </div>
 </sidebar>        
        <div class="main col-md-9" style="width: 78%; padding-left: 0px; margin-left: 0px; margin-right: 0px; padding-right: 0px; border-left: 2px solid #009688;">
        <coverpic class="col-md-12" style="height: 55%;">
            <img src="uploaded_files/<?php echo $cover ?>" class="col-md-12 row" alt="cover_picture" style="border-radius: 5px; border:2px solid #009688;  width: 102%; height: 350px;">
        </coverpic>
            <div class="col-md-12 names" style="text-transform: capitalize; padding-left: 50px; margin-top: -100px;">
                <a class="col-md-4" href="#" style="color: #f8fcf9; text-decoration: none;"><h1><?php echo $Firstname." ".$Lastname ?></h1></a>
            <div class="col-md-5 col-md-offset-3" style="margin-top:30px">
                <?php 
                echo $accept; 
                echo $reject;
                echo $ar;
                echo $rr;                
                echo $cr;
                echo $m;
                ?>
            </div>
            <?php  echo $ep; ?>
            </div>
                        
        
        <user_info class="<?php echo $clas ?> dsply_frnd" style="margin-top: 15px; height:579px; border-right: 2px solid #009688; overflow-y:scroll">
            <div class="bio">
            <div class="ProfileSidebar col-md-12 " >
            <h2 style="color: #009688"> Personal Information </h2>
            <hr/ style="margin-top: 0px; border-color: #009688; border-width: thin;">        
               <div class="row">
             <div class="breakerLine"></div>
            <br>
            </div>
            
             <table class="table table-bordered">  
                <tr>  
                <td><h3>First Name: </h3></td>  
                <td><h3><?php echo $Firstname ?></h3></td>  
                </tr>
                 <tr>  
                <td><h3>Last Name: </h3></td>  
                <td><h3><?php echo $Lastname ?></h3></td>  
                </tr>
                <tr>  
                <td><h3>Gender: </h3></td>  
                <td><h3><?php echo $gender ?></h3></td>  
                </tr>
                 <tr>  
                <td><h3>Country: </h3></td>  
                <td><h3><?php echo $country ?></h3></td>  
                </tr>
                <tr>  
                <td><h3>Date of Birth: </h3></td>  
                <td><h3><?php echo $dob ?></h3></td>  
                </tr>
            </table>  
            <h2 style="color: #009688"> Social Links </h2>
            <hr/ style="margin-top: 0px; border-color: #009688; border-width: thin;">        
               <div class="row">
             <div class="breakerLine"></div>   
            <br>
                </div>
            <p><?php echo $link ?></p>    
            <h2 style="color: #009688"> Contact </h2>
            <hr/ style="margin-top: 0px; border-color: #009688; border-width: thin;">        
               <div class="row">
             <div class="breakerLine"></div>   
            <br>
                </div>
                <?php echo $phone ?>
            <h2 style="color: #009688"> Education </h2>
            <hr/ style="margin-top: 0px; border-color: #009688; border-width: thin;">        
               <div class="row">
             <div class="breakerLine"></div>   
            <br>
                </div>
            <p><?php echo $education ?></p>       
            <h2 style="color: #009688"> Work </h2>
            <hr/ style="margin-top: 0px; border-color: #009688; border-width: thin;">        
               <div class="row">
             <div class="breakerLine"></div>   
            <br>
                </div>
            <p><?php echo $work ?></p>       
            <h2 style="color: #009688"> Skill </h2>
            <hr/ style="margin-top: 0px; border-color: #009688; border-width: thin;">        
               <div class="row">
             <div class="breakerLine"></div>   
            <br>
                </div>
            <p><?php echo $skill ?></p>       
            <h2 style="color: #009688"> Interest </h2>
            <hr/ style="margin-top: 0px; border-color: #009688; border-width: thin;">        
               <div class="row">
             <div class="breakerLine"></div>   
            <br>    
            </div>
                <p><?php echo $interest ?></p>       
            
            </div>
            </div>    
            </user_info>
        <?php echo $r; ?>    
    </div>
</body>
</html>
    