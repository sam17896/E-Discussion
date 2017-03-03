<?php
session_start();
require_once 'classes.php';
require_once 'DBConnect.php'; 
if(!isset($_SESSION['user'])){
    header("location: index.php");
}
$user = new USER();
$db = new Database();
$conn = $db->dbconnection();
$username = $_SESSION['user'];
$user->makeuser($conn,$username);
$error=false;
$nameError='';
$descError='';
$catError='';
$tagError='';
$not=0;
$gr=0;
$z=0;
$noti='';
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
        
if(isset ($_POST['submit'])){
  
  $name = trim($_POST['name']);
  $name = strip_tags($name);
  $name = htmlspecialchars($name);
    
  $description = trim($_POST['description']);
  $description = strip_tags($description);
  $description = htmlspecialchars($description);      
       
  if(empty($name)){
      $error = true;
      $nameError = 'Please enter the name';
  }   
  if(empty($description)){
      $error = true;
      $descError='Please fill out the description';
  }
  if(!$error){
        $stmt = oci_parse($conn,"select topic_seq.nextval from dual");
        oci_execute($stmt);
        $row = oci_fetch_array($stmt);
        $id = $row['NEXTVAL'];
        $stmt = oci_parse($conn,"insert into topic values($id,$user->userid,'$name','$description',sysdate)");
        $row = oci_execute($stmt);
        if($row){
            $number = count($_POST['category']);
             for($i=0; $i<$number; $i++)  
            {  
            if(trim($_POST["category"][$i] != ''))  
            {
                $category = $_POST['category'][$i];
                $stmt =oci_parse($conn,"select * from topiccategory where category='$category' and topic_id=$id");
                oci_execute($stmt);
                if($row=oci_fetch_array($stmt)){
                //    $q = oci_parse($conn,"update userslinks set link='$lin' where usersid=$user and type_id=$typeid");
                //    oci_execute($q);
                }else{
                    $stmt = oci_parse($conn,"insert into topiccategory values($id,'$category')"); //$catid
                    $stat = oci_execute($stmt);
                }
            }
             }
            $number = count($_POST['tag']);
            for($i=0; $i<$number; $i++)  
            {  
            if(trim($_POST["tag"][$i] != ''))  
            {
                $tag = $_POST['tag'][$i];
                $stmt =oci_parse($conn,"select * from tags where tagname='$tag' and topic_id=$id");
                oci_execute($stmt);
                if($row=oci_fetch_array($stmt)){
                //    $q = oci_parse($conn,"update userslinks set link='$lin' where usersid=$user and type_id=$typeid");
                //    oci_execute($q);
                }else{
                    $stmt = oci_parse($conn,"insert into tags values('$tag',$id)");
                    $stat = oci_execute($stmt);
                }
            }
            }
                    $desc =$username." Created the topic ".$name;
                    $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$user->userid,sysdate,'$desc')");
                    oci_execute($activity);
                    $desc =$name." was Created!";
                    $activity = oci_parse($conn,"insert into groupactivity values(gr_act.nextval,$user->userid,$id,sysdate,'$desc')");
                    oci_execute($activity);        
                    $stmt = oci_parse($conn,"insert into topicusers values($id,$user->userid,sysdate)");
                    $result=oci_execute($stmt);
                    if($result){
                        $desc =$username." Added in Topic ".$name;
                        $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$user->userid,sysdate,'$desc')");
                        oci_execute($activity);
                        $activity = oci_parse($conn,"insert into groupactivity values(gr_act.nextval,$user->userid,$id,sysdate,'$desc')");
                        oci_execute($activity);
                    }
               header("location: topic.php?id=$id");
        }
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="content-type" content="text/html" />
	<meta name="author" content="gencyolcu" />

	<title>E - DISCUSSION</title>
    <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/jquery-ui.css"/>
    <link rel="stylesheet" href="css/font-awesome.css">
        <link rel="stylesheet" href="css/popup.css">
    <link rel="stylesheet" href="css/bootstrap-theme.css">
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="css/shashkay.css">
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
            <div class="col-md-5 sc" id="result" style="display:none; max-height:200px; overflow-y:scroll;"></div>
   
        </div>    
    </header>    
       
<div class="container">
    <div class="topic_form col-md-12" style="">
    <h3 style="text-align: center; color: #009688; text-transform: capitalize; font-weight: 400;">Create Topic</h3>
    <hr/ style="border-color: #009688; border-width: thin;">
    <div class="forms" style="margin: auto; width:100%;">
    <form name="detail" id="topic" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post">  
          <div class="">  
               <table class="table table-bordered" id="dynamic_field">  
                   <tr>
                         <td><label class="col-md-12 labels" for="name">Name</label></td>  
                         <td><input type="text" style="margin-top:20px; width:400px; border-radius: 7px;" id="name" name="name" placeholder="Name"/><span class="text-danger"><?php echo $nameError; ?></span> </td>  
                    </tr>
                   <tr>
                         <td><label class="col-md-12 labels" for="description">Discription</label></td>  
                         <td><textarea style="min-width:650px; min-height:100px; margin-top:20px; width:400px; border-radius: 7px;" type="text" id="description" name="description" placeholder="Description"></textarea><span class="text-danger col-md-12"><?php echo $descError; ?></span></td>  
                   </tr>
                    <tr> 
                         <td><label for="category" >Category</label></td>
                         <td id="category"><input style="margin-top:20px; margin-bottom:20px; width:400px; border-radius: 7px;" type="text" name="category[]" placeholder="Enter Category" class="category" /><button style="float:right; margin-top:20px; width:100px;" type="button" name="add" id="add" class="btn btn-success">Add More</button></td>  
                    </tr>  
                    <tr> 
                         <td><label for="tags" >Tags</label></td>
                         <td id="tags"><input style="margin-top:20px; margin-bottom:20px; width:400px; border-radius: 7px;" type="text" name="tag[]" placeholder="Tags" class="tag" /><button style="float:right; width:100px; margin-top:20px;" type="button" name="add" id="addt" class="btn btn-success">Add More</button></td>  
                    </tr>       
               </table>  
               <input style="float:right;" type="submit" name="submit" id="submit" class="btn btn-success" value="Submit" />  
          </div>  
     </form>  
     </div>      
    </div>
    </div>
    <script type="text/javascript">
        var i=1;
        var j=101;
        $("#add").click(function(){
            i++;
           $("#category").append('<div id="row'+i+'"><input style="margin-top:20px; margin-bottom:20px; width:400px; border-radius: 7px;" type="text" name="category[]" placeholder="Enter Category" class="category" /><button style="float:right; margin-top:20px;" type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></div>');
        });
        $("#addt").click(function(){
            j++;
           $("#tags").append('<div id="row'+j+'"><input style="margin-top:20px; margin-bottom:20px; width:400px; border-radius:7px;"type="text" name="tag[]" placeholder="Tags" class="tag" /><button style="float:right; margin-top:20px;" type="button" name="remove" id="'+j+'" class="btn btn-danger btn_remove">X</button></div>');
        });
        $(document).on('click', '.btn_remove', function(){  
           var button_id = $(this).attr("id");   
           $('#row'+button_id+'').remove();  
      });
        catupdated();
        tagupdated();
        
      var tag =[];
      var cat = [];
      
        function catupdated(){
                setTimeout(function(){
                   updatecat();
                   catupdated();
                },2000);
            }                  
        
      function updatecat(){
            $.getJSON("cat.php",function(data){
              cat=[];
              var k=0;
                $.each(data.cat,function(){
                    console.log(this['cat']);
                    cat[k] = this['cat'];
                    k++;
                });
                console.log(cat);
            });
              $( ".category" ).autocomplete({
          source: cat
        });
    }
        function tagupdated(){
                setTimeout(function(){
                   updatetag();
                   tagupdated();
                },2000);
            }                  
        
      function updatetag(){
            $.getJSON("tag.php",function(data){
              tag=[];
              var k=0;
                $.each(data.tag,function(){
                    console.log(this['tag']);
                    tag[k] = this['tag'];
                    k++;
                });
                console.log(tag);
            });
              $( ".tag" ).autocomplete({
          source: tag
        });
   }
    </script>
</body>
</html>