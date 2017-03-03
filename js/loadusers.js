$(document).ready(function(){
            updateusers();
            usersloaded();
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
                        $(\"#mem\").append(\"<li><a href='profile.php?id=\"+this['userid']+\"'>\"+this['username']+\" (Admin)</a></li>\");
                    }else{
                    $(\"#mem\").append(\"<li><a href='profile.php?id=\"+this['userid']+\"'>\"+this['username']+\"</a></li>\");
                    }if($user->userid==$topic1->adminid&&this['userid']!=$topic1->adminid){
                        $(\"#mem\").append(\"<a href='remove.php?id=\"+this['id']+\"&userid=\"+this['userid']+\"'><button>Remove</button></a>\");
                    }
                    ";   
                    ?>
                        
                   });
               }); 
            }

