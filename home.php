<?php
 ob_start();
 session_start();
 require_once 'DBconnect.php';
require_once 'classes.php';
$topic='';
$not=0;
$gr=0;
$z=0;
$noti='';
$dp='';
 if( !isset($_SESSION['user']) ) {
      header("Location: index.php");
  exit;
 }else{
     $db = new Database();
     $conn = $db->dbConnection(); 
     $user = new USER();
     $username = $_SESSION['user'];
     $feed = new newsfeed();
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
     $stmt=pg_query($conn,"select userpic from users where usersid=$user->userid");
     pg_fetch_array($stmt);
     $row=pg_fetch_array($stmt);
     $dp=$row['USERPIC'];
     $stmt = pg_query($conn,"select detail from notification where usersid=$user->userid order by time desc");
     pg_fetch_array($stmt);
     while($row=pg_fetch_array($stmt)){
         $noti .= "<p>".$row['DETAIL']."</p><hr>";
     }
}
?>
<html>
    <head>
        <title>Home</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/jquery-ui.css"/>
        <link rel="stylesheet" href="css/shashkay.css"/>
        <link rel="stylesheet" href="css/font-awesome.css"/>
        <link rel="stylesheet" href="css/popup.css"/>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery-1.11.0.min.js"></script>
        <script src="js/jquery-ui.js"></script>
        <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css"/>
                 
<script>
$(document).ready(function(){    
    var id = <?php echo $user->userid ?>;
        var name;
        $.getJSON("thread.php?id="+id,function(data){
           if(data=="no"){
               
           }else{
               $("#thread").empty();
               $.each(data.thread,function(){
                   if(this['msg']==1){
                      $("#thread").append("<a href='message.php?userid="+this['id']+"&name="+this['username']+"' class='anchor' name='"+this['id']+"'><h3><strong>"+this['username']+"* </strong></h3></a><p>"+this['message']+"</p><hr/ style='margin: 0px;'>");
                    }else{
                        $("#thread").append("<a href='message.php?userid="+this['id']+"&name="+this['username']+"' class='anchor' name='"+this['id']+"'><h3>"+this['username']+"</h3></a><p>"+this['message']+"</p><hr/ style='margin: 0px;'>");
                    }
               });
           }
        });
 
updatetopic();
topicloaded();
updateact();
actloaded();
updatefriends();
friendsupdated();
updategroups();
groupsupdated();
        
});
function topicloaded(){
    setTimeout(function(){
       updatetopic();
       topicloaded();
    },2000);
}                  
function actloaded(){
    setTimeout(function(){
       updateact();
       actloaded();
    },2000);
}
    function friendsupdated(){
                setTimeout(function(){
                   updatefriends();
                   friendsupdated();
                },10000);
            }                  
        
        function updatefriends(){
            var uid = <?php echo $user->userid; ?>;
            
            $.getJSON("userfriends.php?id="+uid,function(data){
                $("#friends").empty();
                $.each(data.friends,function(){
                    if(this['line']==1){
                        $("#friends").append("<a href='profile.php?id="+this['id']+"'><img src='uploaded_files/" +this['userpic']+"' style=\"width:25px; height:25px; margin-right:6px;\" /><span>"+this['name']+"<span><img src='imgs/online.png' style='width:10px; height:10px; float:right; margin-top:10px'/></a><hr/>");
                    }else{
                        $("#friends").append("<a href='profile.php?id="+this['id']+"'><img src='uploaded_files/" +this['userpic']+"' style=\"width:25px; height:25px; margin-right:6px;\" /><span>"+this['name']+"<p></span><hr/>");
                    }
            });
       });
        }
        function groupsupdated(){
                setTimeout(function(){
                   updategroups();
                   groupsupdated();
                },1000);
            }                  
        
        function updategroups(){
            var uid = <?php echo $user->userid; ?>;
            $.getJSON("usertopic.php?id="+uid,function(data){
                $("#groups").empty();
                $.each(data.topics,function(){
                    console.log(this['msg']);
                    if(this['msg']==0){
                        $("#groups").append("<a href='topic.php?id="+this['id']+"'><p>"+this['name']+"</p></a><hr/>");
                    }else{
                        $("#groups").append("<a href='topic.php?id="+this['id']+"'><p><strong>"+this['name']+"* </strong></p></a><hr/>");
                    }
            });
       });
        }

function updateact(){
    $.getJSON("loadact.php",function(data){
       $("#act").empty();
        $.each(data.ract,function(){
           $("#act").append("<p>"+this['detail']+"</p><hr/>"); 
        });
    });
}    
function updatetopic(){
    $.getJSON("loadtopic.php",function(data){
       $("#topic").empty();
        $.each(data.topic,function(){
           $("#topic").append("<a href='topic.php?id="+this['id']+"'><h1>"+this['name']+"</h1></a><p><strong>Admin:</strong> <a href='profile.php?id="+this['admin']+"'>"+this['username']+"</a><p><strong>Description: </strong>"+this['description']+"<hr/>");
                    
       });
   }); 
}  
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
    <body style="background: #ddd;">   
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
            
        </div>    
    </header>
    <div class="container-fluid">
    <sidebar style="height: 100%">
        <div class="col-md-3" style="background: #d3d3d3; border-right: 2px solid #009688">
            <h3 style="color: #009688;"><?php echo $user->username ?></h3>
            <hr/ style="margin: 0px;">
            <div class="embed-responsive embed-responsive-16by9">
                <img class="embed-responsive-item" src="uploaded_files/<?php echo $dp ?>" style="height: 80%; width: 60%; border-radius: 8px; border: 2px solid #00796B; margin-left: 20%; margin-top: 10%;"/>
            </div>
            <div id='fri' class="input-group" style="width: 100%; border-radius: 5px;">
                <h3 style="color: #009688;">Friends</h3>
                <hr/ style="margin: 0px;">
                <div id="friends" class='sc' style="width: 100%; height: 150px; overflow-y: scroll;">
                </div>
            </div>
            <div id='gr' style="border-radius: 5px;">
                <h3  class="col-md-6" style="color: #009688;">Topics (<?php echo $gr ?>)</h3>
                <a class="col-md-6" style="float:right; width=100%; margin-top:20px;" href='createtopic.php'><input type="button" value="Create Topic"/></a>
                <hr/ style="margin: 0px; width=100%;">
                <div id='groups' class="sc" style="margin-top: 3%; height: 166px; width: 100%; overflow-y: scroll;">
                </div>
            </div>
        </div>  
    </sidebar>
    
    <middlebar style="height: 100%;">
        <div class="mid col-md-6" style="color: #009688; border-right: 2px solid #009688">
            <h3>Newsfeed</h3>
            <hr/ style="margin: 0px;">
            <div id='topic' class="sc" style="height: 539px; overflow-y: scroll;">
            </div>
        </div>
    </middlebar>
    
    <lastbar style="height: 100%;">
        <div class="last col-md-3" style="color: #009688; border-radius: 5px;">
        <h3>Recent Activities</h3>
            <hr/ style="margin: 0px;">
            <div id='act' class="sc" style="height: 30%; overflow-y: scroll;">
                   
            </div>
        <h3>Chat</h3>
            <hr/ style="margin: 0px;">
            <div id="thread" class="sc" style="height: 280px; color: #009688; overflow-y: scroll; border-radius: 5px;">
                   
            </div>            
        </div>
        </lastbar>    
    </body>
</html>