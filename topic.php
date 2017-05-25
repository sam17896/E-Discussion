<?php
session_start();
require_once 'classes.php';
require_once 'DBConnect.php';
$db = new Database();
$conn = $db->dbConnection();
$topic1 = new topic();
$user = new USER();
$admin=0;
$not=0;
$z=0;
$noti='';
if(!isset($_SESSION['user'])){
    header("location: index.php");
}
else{
    $username=$_SESSION['user'];
    $user->makeuser($conn,$username);
}
if(empty($_GET['id'])){
    header("location: home.php");
}
else{
    $id = $_GET['id'];
    $_SESSION['topic']=$id;
    $stmt = pg_query($conn,"select count(*) from messagenot where usersid = $user->userid");
     pg_fetch_array($stmt);
     $row=pg_fetch_array($stmt);
     $not=$row['COUNT(*)'];
    $stmt = pg_query($conn,"select count(*) from notification where usersid = $user->userid and status=0");
     pg_fetch_array($stmt);
     $row=pg_fetch_array($stmt);
     $z = $row['COUNT(*)'];
      $stmt = pg_query($conn,"select detail from notification where usersid=$user->userid order by time desc");
     pg_fetch_array($stmt);
     while($row=pg_fetch_array($stmt)){
         $noti .= "<p>".$row['DETAIL']."</p><hr>";
     }
    if(!$topic1->isTopic($conn,$id)){
        echo 'The link you requested is not available';
        header("location: home.php");
    }
    else{
        $topic1->maketopic($conn,$id);
        $topic1->clearnot($conn,$id,$user->userid);
        if($user->userid==$topic1->adminid){
            $admin = 1;
        }        
    }
}
?>
<html>
    <head>
        <title>Group</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/topic.css"/>
        <link rel="stylesheet" href="css/font-awesome.css">
        <link rel="stylesheet" href="css/popup.css">
        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery-1.11.0.min.js"></script>
        <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css"/>
        <script type="text/javascript"> 
        $(document).ready(function(){
            $("#send").click(function(){
               var mess = $("#mess").val();
                $.get("sendmessage.php?message="+mess,function(){
                    $("#mess").val('');
                });
            });
                $("#mess").keypress(function(e){
                    if(e.which==13||e.keyCode==13){
                        var mess = $("#mess").val();
                        $.get("sendmessage.php?message="+mess,function(){
                            $("#mess").val('');
                        });
                    }
                });
    var admin = <?php echo $admin ?>;
    console.log(admin);
    if(admin==1){
        console.log(admin);
        $("#ms").removeClass('col-md-9').addClass('col-md-6');
        $("#adduser").show();
        $("#request").show();
        updatefriends();
        friendsloaded();
        done();
    }
            updatemessage();
            messageloaded();
            updatepage();
            updateusers();
            updatebutton();
            buttonloaded();
            usersloaded();
            updatemess();
            messloaded();
            pageloaded();
            });
            function usersloaded(){
                setTimeout(function(){
                   updateusers();
                   usersloaded();
                },5000);
            }                  
            function updateusers(){
                $.getJSON("loadusers.php",function(data){
                   $("#mem").empty();
                   $.each(data.member,function(){
                    <?php   
                    echo "if(this['userid']==$topic1->adminid){
                        $(\"#mem\").append(\"<a href='profile.php?id=\"+this['userid']+\"'>\"+this['username']+\" (Admin)</a><br><hr/>\");
                    }else if($user->userid==$topic1->adminid&&this['userid']!=$topic1->adminid){
                    $(\"#mem\").append(\"<a href='profile.php?id=\"+this['userid']+\"'>\"+this['username']+\"</a><a href='remove.php?id=\"+this['id']+\"&userid=\"+this['userid']+\"'><button style='float:right'>Remove</button></a><br><div class='clear'></div><hr/>\");
                    }else{
                        $(\"#mem\").append(\"<a href='profile.php?id=\"+this['userid']+\"'>\"+this['username']+\"</a><br><hr/>\");
                    }
                    ";   
                    ?>
                        
                   });
               }); 
            }
            function pageloaded(){
                setTimeout(function(){
                   updatepage();
                   pageloaded();
                },2000);
            }
            function updatepage(){
                var intopic="no";
                $.get("inTopic.php");
                $.get("inTopic.php",function(data){
                        if (data=="yes"){
                         $("#sec").show();
                        }else
                            {
                            $("#sec").hide();
                        }
                    });
            }
            function messloaded(){
                setTimeout(function(){
                   updatemess();
                   messloaded();
                },2000);
            }                  
            function updatemess(){
                        $.get("inTopic.php");
                        $.get("inTopic.php",function(data){
                            if(data=="yes"){
                                $("#ms").show();
                        }else{
                                $("#ms").hide();
                        }
                        });
                }
            function buttonloaded(){
                setTimeout(function(){
                   updatebutton();
                   buttonloaded();
                },5000);
            }                  
            function updatebutton(){
                        $.get("isrequest.php",function(data){
                                     console.log(data);  
                            if(data=="yes"){
                                   $("#join").hide();
                                    $("#cancel").show();
                                     $("#leave").hide();
                                }else{
                                    $.get("inTopic.php");
                                    $.get("inTopic.php",function(data){
                                        if(data=="yes"){
                                    $("#join").hide();
                                    $("#cancel").hide();
                                     $("#leave").show();
                                    }else{
                                        $("#cancel").hide();
                                        $("#leave").hide();
                                        $("#join").show();
                                    }
                        });
 
                                }
                              });
                }
            function friendsloaded(){
    setTimeout(function(){
       updatefriends();
       friendsloaded();
    },5000);
}
            function messageloaded(){
    setTimeout(function(){
       updatemessage();
       messageloaded();
    },200);
}                  
function updatemessage(){
        console.log('hi');
    
    $.getJSON("loadmessage.php",function(data){
       $("#msg").empty();
        console.log('hi');
       $.each(data.messages,function(){
           if(this['user']==1) {
            $("#msg").append("<p class='right'><strong>"+this['username']+":</strong> "+this['text']+"</p><br><div class='clear'></div>");
           }else{
            $("#msg").append("<p><strong>"+this['username']+":</strong> "+this['text']+"</p><br><div class='clear'></div>");           
           }
       });
   }); 
}

function updatefriends(){
    $.getJSON("loadfriend.php",function(data){
       $("#users").empty();
       $.each(data.friend,function(){
            $("#users").append("<div style='margin-bottom:10px; margin-top:10px;'><a href='profile.php?id="+this['userid']+"'>"+this['username']+"</a><a href='add_user.php?id="+this['id']+"&userid="+this['userid']+"'><button style='float:right'>Add</button></a><br><div class='clear'></div><hr/></div>");
       });
   }); 
}
function done(){
    setTimeout(function(){
       update();
       done();
    },1000);
}                  
function update(){   
    $.getJSON("loadpermission.php",function(data){
       $("#req").empty();
       
       $.each(data.permission,function(){
           $("#req").append("<div style='margin-bottom:10px; margin-top:10px;'><a href='profile.php?id="+this['userid']+"'>"+this['username']+"</a><a href='add_user.php?id="+this['id']+"&userid="+this['userid']+"'><button style='float:right'>Accept</button></a><a href='reject.php?id="+this['id']+"&userid="+this['userid']+"'><button style='float:right'>Reject</button></a><br><div class='clear'></div><hr/></div>");
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
                            <a href="profile.php?id=<?php echo $user->userid ?>"><i class="fa fa-user acc" aria-hidden="true" style=" margin-right: 15px; color: #eee; font-size: 20px;"></i></a>
                            <a href="home.php"><i class="fa fa-home" aria-hidden="true" style=" margin-right: 15px; color: #eee; font-size: 20px;"></i></a>
                            <a href="message.php?id=<?php echo $user->userid ?>"><i class="fa fa-comments" aria-hidden="true" style=" margin-right: 15px; color: #eee; font-size: 20px;"><span class="badge"><?php echo $not ?></span></i></a>
                            <a id="notificationLink" href="#"><i class="fa fa-bell" aria-hidden="true" style=" margin-right: 15px; color: #eee; font-size: 20px;"><span id="notification_count" class="badge"><?php echo $z ?></span>
                            <div id="notificationContainer"><div id="notificationTitle" ><h3>Notifications</h3></div><div id="notificationsBody" class="sc"><?php echo $noti ?></div></div>
                            </i></a>
                            <a href="logout.php"><i class="fa fa-lock log" aria-hidden="true" style=" margin-right: 15px; color: #eee; font-size: 20px;"></i></a>
                        </ul>
                </nav>
            </div>
            <div class="col-md-3"></div>
            <div class="col-md-5 sc" id="result" style="display:none; max-height:200px; overflow-y:scroll;"></div>
   
        </div>    
    </header>
        
    <div class="container-fluid">
    <sidebar style="height: 100%">
        <div class="col-md-3" style="background: #d3d3d3; border-right: 2px solid #009688">
                <h3 class="col-md-6" style="color: #009688;"><?php echo $topic1->name ?></h3>
                <div class="col-md-6">
                <a href='request.php?id=<?php echo $id?>'><button id='join' style ='display:none; float:right; margin-top:20px;'>Join Group</button></a>
                <a href='request.php?id=<?php echo $id?>&delete=1'><button id='cancel' style ='display:none; float:right; margin-top:20px;'>Cancel</button></a>
                <a href='left.php?id=<?php echo $id?>'><button id='leave' style ='display:none; float:right; margin-top:20px;'>Leave Group</button></a>
            </div>    
        
                <hr/ style="margin: 0px; width:100%;">
            <div class="embed-responsive embed-responsive-16by9">
                <img class="embed-responsive-item" src="imgs/userpic.jpg" style="height: 80%; width: 60%; border-radius: 8px; border: 2px solid #00796B; margin-left: 20%; margin-top: 10%;"/>
            </div>
            <div class="input-group" style="background: #d3d3d3; width: 100%;">
                <h3 style="color: #009688;">Group Description</h3>
                <hr/ style="margin: 0px;">
                <input type="text" class="form-control" placeholder="Description" style="margin-top: 3%;" aria-describedby="basic-addon1" value="<?php echo $topic1->description?>" disabled>
            </div>
            <div style="background: #d3d3d3;">
                <h3 style="color: #009688;">Members</h3>
                <hr/ style="margin: 0px;">
                <div id='mem' class="sc" style="margin-top: 3%; height: 203px; width: 100%; overflow-y: scroll;">
                </div>
            </div>
        </div>  
    </sidebar>
    <middlebar id='ms' style="height: 100%; color: #009688; border-right: 2px solid #009688; display:none" class="mid col-md-9">
        
        <h3>Messages</h3>    
        <hr/ style="margin: 0px; width: 100%;">
        <div id='msg' class="sc" style=" width: 100%; height: 380px; overflow-y: scroll;">
        </div>
            <div id='sec' style="display:none">
                
                <textarea type="text" id="mess" name="message" class="sc msg" placeholder="Type a message..." style="overflow-y: scroll; width: 100%; height: 125px;"></textarea>
                <button type="send" name="send" id="send" class="btn btn-default" style="background: #009688; color: #d3d3d3; width: 100%">Send</button>
            </div>
        
    </middlebar>
    
    <lastbar style="height: 100%">
        <div class="last col-md-3" style="color: #009688;">
        <div id='request' style="display:none">
            <h3>Requests</h3>
            <hr/ style="margin: 0px;">
            <div id="req" class="sc" style="height: 240px; overflow-y: scroll;">
                   
            </div>
        </div>
        <div id="adduser" style="display:none">    
        <h3>Add Friends</h3>
            <hr/ style="margin: 0px;">
            <div id="users" class="sc" style="height: 240px; overflow-y: scroll;">
                   
            </div>
        </div>
        </div>    
    </lastbar>
        </body>
</html>