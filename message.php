<?php
session_start();
require_once 'classes.php';
require_once 'DBConnect.php';
$db = new Database();
$conn = $db->dbConnection();
$topic1 = new topic();
$thread = new thread();
$function=0;
$id=0;
$userid=0;
$name='';
$not=0;
$z=0;
$noti='';
$user = new USER();
if(!isset($_SESSION['user'])){
    header("location: index.php");
}
else{
    $username=$_SESSION['user'];
    $user->makeuser($conn,$username);
    $stmt = oci_parse($conn,"select count(*) from messagenot where usersid = $user->userid and thread_id is not null");
         oci_execute($stmt);
         $row=oci_fetch_array($stmt);
         $not=$row['COUNT(*)'];
         $stmt = oci_parse($conn,"select count(*) from messagenot where usersid = $user->userid and topic_id is not null");
         oci_execute($stmt);
         $row=oci_fetch_array($stmt);
         $gr = $row['COUNT(*)'];
    $stmt = oci_parse($conn,"select count(*) from notification where usersid = $user->userid and status=0");
     oci_execute($stmt);
     $row=oci_fetch_array($stmt);
     $z = $row['COUNT(*)'];
      $stmt = oci_parse($conn,"select detail from notification where usersid=$user->userid order by time desc");
     oci_execute($stmt);
     while($row=oci_fetch_array($stmt)){
         $noti .= "<p>".$row['DETAIL']."</p><hr>";
     }
        
}
if(empty($_GET['id'])){
    if(empty($_GET['userid'])){
        header("location: home.php");
    }else{
        $function = 1;
        $userid=$_GET['userid'];
        $name = $_GET['name'];
        $thread->clearnot($conn,$userid,$user->userid);
    }
}
else{
    $id = $_GET['id'];
    if($user->userid==$id){
    }
    else{
        header("location: home.php");
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
<title>Welcome - <?php echo $_SESSION['user']; ?></title>
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-theme.css">
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css"/>
        <link rel="stylesheet" href="css/shashkay.css">
        <link rel="stylesheet" href="css/topic.css">
        <link rel="stylesheet" href="css/font-awesome.css">
        <link rel="stylesheet" href="css/popup.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="js/jquery-1.11.0.min.js"></script>    
<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
    $(document).ready(function(){
        var id;
        var name;
        $.getJSON("thread.php",function(data){
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
               
               $(".anchor").click(function(){
                   id = $(this).attr("name");
                   name=$(this).text();
                   console.log(name);
                   $.get("clearnot.php?id="+id);
                    $.getJSON("loadthreadmsg.php?id="+id,function(data){
                        $("#msg").empty();
                        $("#sec").show();
                        $.each(data.msg,function(){
                           if(this['user']==1) {
            $("#msg").append("<p class='right'><strong>"+this['username']+":</strong> "+this['text']+"</p><br><div class='clear'></div>");
           }else{
            $("#msg").append("<p><strong>"+this['username']+":</strong> "+this['text']+"</p><br><div class='clear'></div>");           
           } 
                        });
                    });
                   updatemsg();
                });
           } 
        });
        function updatemsg(){
            setTimeout(function(){
                   msgupdate(id);
                   updatemsg();
                },2000);
        }
        function msgupdate(id){
            $.getJSON("loadthreadmsg.php?id="+id,function(data){
                        $("#msg").empty();
                        $("#sec").show();
                        $.each(data.msg,function(){
                           if(this['user']==1) {
            $("#msg").append("<p class='right'><strong>"+this['username']+":</strong> "+this['text']+"</p><br><div class='clear'></div>");
           }else{
            $("#msg").append("<p><strong>"+this['username']+":</strong> "+this['text']+"</p><br><div class='clear'></div>");           
           } 
                        });
                    });
        }
        $("#send").click(function(){
               var mess = $("#mess").val();
                $.get("sendthreadmsg.php?message="+mess+"&id="+id,function(){
                    $("#mess").val('');
                    
                });
            });
                $("#mess").keypress(function(e){
                    if(e.which==13||e.keyCode==13){
                        var mess = $("#mess").val();
                        $.get("sendthreadmsg.php?message="+mess+"&id="+id,function(){
                            $("#mess").val('');
                            
                       });
                    }
                });  
        var func = <?php echo $function; ?>;
        var user;
        if(func==1){
            user = <?php echo $userid; ?>;
            id= user;
            name="<?php echo $name; ?>";
            console.log('if');
            $.getJSON("loadthreadmsg.php?id="+user,function(data){
                        var nam = "<?php echo $name; ?>";
                        $("#msg").empty();
                        $("#sec").show();
                        console.log('get');
                        $.each(data.msg,function(){
                           if(this['user']==1) {
            $("#msg").append("<p class='right'><strong>"+this['username']+":</strong> "+this['text']+"</p><br><div class='clear'></div>");
           }else{
            $("#msg").append("<p><strong>"+this['username']+":</strong> "+this['text']+"</p><br><div class='clear'></div>");           
           } 
                        });
                    });
                   updatemsg();
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
            <div class="col-md-5 sc" id="result" style="display:none; max-height:200px; overflow-y:scroll;"></div>
   
        </div>    
    </header>    
       <div class="container-fluid">
            <div class="chats col-md-3">
                <h3>Chats</h3>
                <div id='thread' class="sc" style="width: 100%; height: 500px; overflow-y: scroll;"></div>
            </div>
            <hr/ style="margin: 0px;">
            <div class="msgg col-md-9" style="border-left: 2px solid #009688;">
                <div class="messages sc" style=" width: 100%; height: 380px; overflow-y: scroll;">
                <div id='head'></div>
                <hr/ style="margin: 0px; width: 100%;">
                <div id='msg'></div>
                </div>
                <div id='sec' style="display:none">
                <textarea type="text" id="mess" name="message" class="msg sc" placeholder="Type a message..." style="overflow-y: scroll; width: 100%; height: 125px;"></textarea>
                <button type="send" name="send" id="send" class="btn btn-default" style="background: #009688; color: #d3d3d3; width: 100%">Send</button>
            </div>
            </div>
        </div>
</body>