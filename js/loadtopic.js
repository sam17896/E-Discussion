$(document).ready(function(){
updatetopic();
topicloaded();
});
function topicloaded(){
    setTimeout(function(){
       updatetopic();
       topicloaded();
    },2000);
}                  
function updatetopic(){
    $.getJSON("loadtopic.php",function(data){
       $("#topic").empty();
        $("#topic").append("<h1>Topics </h1>");
        var topic = <?php echo $topic1->inTopic($conn,$user->userid,$id);?>;
     if(topic==1){    
       $.each(data.topic,function(){
           $("#msg").append("<li><a href='topic.php?id="+this['id']+"'>"+this['name']+"</a><br><p>Created By: <a href='profile.php?id="+this['admin']+"'>"+this['username']+"</a><br><p>Description: +"+this['description']+"</li>");
           

           
           
       });
     }
   }); 
}

         if($user->userid==$admin){
             $topic .="<a href='topic.php?id=$id'><button id='btn' name='add'>Add Users</button></a><br>";
         }else{
            $stmt1 = oci_parse($conn,"select * from permission where topic_id=$id and usersid=$user->userid and status_2=0");
            oci_execute($stmt1);
            if($rows=oci_fetch_array($stmt1)){
                $topic .="<a href='request.php?id=$id&delete=1'><button id='btn' name='request' >Cancel</button><br></a>";
            }else{
            $stmt2 = oci_parse($conn,"select * from topicusers where topic_id=$id and usersid=$user->userid");
            oci_execute($stmt2);
            if($res=oci_fetch_array($stmt2)){
                $topic .="Added in the group";
            }
            else{
                $topic .="<a href='request.php?id=$id'><button id='btn' name='request' >Join Group</button><br></a>";
            }
            }
            
         }
        