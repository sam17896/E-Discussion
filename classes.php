<?php
require_once 'DBConnect.php';
class topic{
    public $id='';
    public $adminid='';
    public $name='';
    public $description='';
    public $con;
 public function __construct(){
  $database = new Database();
  $db = $database->dbConnection();
  $this->con = $db;
}
    public function isTopic($conn,$id){
        $stmt= oci_parse($conn,"select * from topic where id='$id'");
        oci_execute($stmt);
        if($row=oci_fetch_array($stmt)){
            return true;
        }
        else{
            return false;
        }
    }
public function maketopic($conn,$id){
        $this->con = $conn;
        $this->id = $id;
        $stmt = oci_parse($conn,"select * from topic where id='$id'");
        oci_execute($stmt);
        $row=oci_fetch_array($stmt);
        $this->adminid=$row['ADMINID'];
        $this->name=$row['NAME'];
        $this->description=$row['DESCRIPTION'];
    }
public function getUsers($conn,$id){
    $stmt = oci_parse($conn,"select t.usersid,u.username from topicusers t,users u where t.topic_id=$id and t.usersid=u.usersid");
    oci_execute($stmt);
    $result=array();
    while(($row=oci_fetch_array($stmt))!=false){
        $userid=$row['USERSID'];
        $username = $row['USERNAME'];
        array_push($result,array('userid'=>$userid,'username'=>$username,'id'=>$id));
    }
    echo json_encode(array('member'=>$result));
}
    public function getAvailableUsers($conn,$id){
        $stmt = oci_parse($conn,"select username,usersid from users where usersid in ((select friendid from usersfriend) minus (select usersid from topicusers where topic_id=$id))");
        oci_execute($stmt);
        $result=array();
        while(($row=oci_fetch_array($stmt))!=false){
            $username=$row['USERNAME'];
            $userid=$row['USERSID'];
            array_push($result,array('username'=>$username,'userid'=>$userid,'id'=>$id));
    }
        echo json_encode(array('friend'=>$result));
    }
    public function adduser($conn,$id,$userid){
        $stmt = oci_parse($conn,"insert into topicusers values($id,$userid,sysdate)");
        $res=oci_execute($stmt);
        $stmt = oci_parse($conn,"select username from users where usersid=$userid");
        oci_execute($stmt);
        $row = oci_fetch_array($stmt);
        $username = $row['USERNAME'];
        $desc =$username." Added in Topic ".$this->name;
        $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$userid,sysdate,'$desc')");
        oci_execute($activity);
        $activity = oci_parse($conn,"insert into groupactivity values(act_seq.nextval,$userid,$id,sysdate,'$desc')");
        oci_execute($activity);
        $uname = $_SESSION['user'];
        $desc =$uname." Add ".$username." in Topic ".$this->name;
        $user = new USER();
        $user->makeuser($conn,$uname);
        $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$user->userid,sysdate,'$desc')");
        oci_execute($activity);
        $activity = oci_parse($conn,"insert into groupactivity values(act_seq.nextval,$user->userid,$id,sysdate,'$desc')");
        oci_execute($activity);
    }
    public function removeuser($conn,$id,$userid){
        $stmt = oci_parse($conn,"delete from topicusers where topic_id=$id and usersid=$userid");
        $res=oci_execute($stmt);
        $stmt = oci_parse($conn,"select username from users where usersid=$userid");
        oci_execute($stmt);
        $row = oci_fetch_array($stmt);
        $username = $row['USERNAME'];
        $desc =$username." Removed from Topic ".$this->name;
        if($this->isRequested($conn,$id,$userid)){
            $this->removePermission($conn,$id,$userid);
        }
        $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$userid,sysdate,'$desc')");
        oci_execute($activity);
        $activity = oci_parse($conn,"insert into groupactivity values(act_seq.nextval,$userid,$id,sysdate,'$desc')");
        oci_execute($activity);
        $uname = $_SESSION['user'];
        $desc =$uname." Remove ".$username." from Topic ".$this->name;
        $user = new USER();
        $user->makeuser($conn,$uname);
        $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$user->userid,sysdate,'$desc')");
        oci_execute($activity);
        $activity = oci_parse($conn,"insert into groupactivity values(act_seq.nextval,$user->userid,$id,sysdate,'$desc')");
        oci_execute($activity);
    }
    public function isRequested($conn,$id,$userid){
        $stmt = oci_parse($conn,"select * from permission where topic_id=$id and usersid=$userid and (status_2=0 or status_2=1)");
        oci_execute($stmt);
        if($row=oci_fetch_array($stmt))
            return true;
        else
            return false;
    }
    public function makenewadmin($conn,$id,$userid){
        $this->leavegroup($conn,$id,$userid);
        $stmt = oci_parse($conn,"select usersid,count(*) from groupactivity where usersid in (select usersid from topicusers where topic_id=$id) group by usersid having count(*)>=all(select count(*) from groupactivity where usersid in (select usersid from topicusers where topic_id=$id) group by usersid)");
        oci_execute($stmt);
        $count=0;
        while($row=oci_fetch_array($stmt)){
            $count=$count+1;
        }
        oci_execute($stmt);
        if($count>0){
        $row=oci_fetch_array($stmt);    
        $user = $row['USERSID'];
        $stmt = oci_parse($conn,"select username from users where usersid=$user");
        oci_execute($stmt);
        $row=oci_fetch_array($stmt);
        $desc = $row['USERNAME']." is new admin of ".$this->name;
        $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$user,sysdate,'$desc')");
        oci_execute($activity);
        $activity = oci_parse($conn,"insert into groupactivity values(act_seq.nextval,$user,$id,sysdate,'$desc')");
        oci_execute($activity);
        $query = oci_parse($conn,"update topic set adminid=$user where id=$id");
        oci_execute($query);
        }else{
            $stmt = oci_parse($conn,"delete from topicusers where topic_id=$id");
            oci_execute($stmt);
            $stmt=oci_parse($conn,"delete from permission where topic_id =$id");
            oci_execute($stmt);
            $stmt=oci_parse($conn,"delete from tags where topic_id =$id");
            oci_execute($stmt);
            $stmt=oci_parse($conn,"delete from topicmessage where topic_id =$id");
            oci_execute($stmt);
            $stmt=oci_parse($conn,"delete from topiccategory where topic_id =$id");
            oci_execute($stmt);
            $stmt=oci_parse($conn,"delete from groupactivity where topicid =$id");
            oci_execute($stmt);
            $stmt=oci_parse($conn,"delete from topic where id =$id");
            oci_execute($stmt);
            header ("location: home.php");
        }
    }
    public function leavegroup($conn,$id,$userid){
        if($this->isRequested($conn,$id,$userid)){
            $this->removePermission($conn,$id,$userid);
        }
            $stmt = oci_parse($conn,"delete from topicusers where topic_id=$id and usersid=$userid");
            oci_execute($stmt);
            $username = $_SESSION['user'];
            $desc =$username." has left the Topic ".$this->name;
            $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$userid,sysdate,'$desc')");
            oci_execute($activity);
            $activity = oci_parse($conn,"insert into groupactivity values(act_seq.nextval,$userid,$id,sysdate,'$desc')");
            oci_execute($activity);
    }
    public function isRequest($conn,$id,$userid){
        $stmt = oci_parse($conn,"select * from permission where topic_id=$id and usersid=$userid and status_2=0");
        oci_execute($stmt);
        if($row=oci_fetch_array($stmt))
            return "yes";
        else
            return "no";
    }
    
    public function removePermission($conn,$id,$userid){
        $stmt = oci_parse($conn,"delete from permission where topic_id=$id and usersid=$userid and (status_2=0 or status_2=1)");
        oci_execute($stmt);
  } 
    public function updatePermission($conn,$id,$userid){
        $stmt = oci_parse($conn,"update permission set status_2=1 where topic_id=$id and usersid=$userid and status_2=0");
        oci_execute($stmt);
        
  }
    public function getMessages($conn,$id){
        $stmt =oci_parse($conn,"select u.username,m.messagetext,m.time from topicmessage t,message m ,users u where t.topic_id=$id and t.message_id = m.id and m.senderid=u.usersid order by m.time");
        $res=oci_execute($stmt);
        $result = array();
        $user=0;
        while($row=oci_fetch_array($stmt)){
            if($row['USERNAME']==$_SESSION['user']){
                $user=1;
            }
            array_push($result,array('username'=>$row['USERNAME'],
                                    'text'=>$row['MESSAGETEXT']->load(),
                                    'time'=>$row['TIME'],'user'=>$user));
            $user=0;
        }
        echo json_encode(array('messages'=>$result));
    }
    public function inTopic($conn,$id,$tid){
        $stmt = oci_parse($this->con,"select * from topicusers where topic_id=$tid and usersid=$id");
        oci_execute($stmt);
        if($row=oci_fetch_array($stmt)){
            return "yes";
        }
        else{
            return "no";
        }
    }
    public function getPermission($conn,$id){
        $stmt = oci_parse($conn,"select usersid from permission where topic_id=$id and status_2=0");
        oci_execute($stmt);
        $result =array();
        while(($row=oci_fetch_array($stmt))!=false){
            $userid=$row['USERSID'];
            $query = oci_parse($conn,"select username from users where usersid=$userid");
            oci_execute($query);
            $res = oci_fetch_array($query);
            $username=$res['USERNAME'];
            array_push($result,array('username'=>$res['USERNAME'],
                                    'userid'=>$row['USERSID'],
                                    'id'=>$id));
        }
        echo json_encode(array('permission'=>$result));
    }
    public function addmessage($conn,$id,$mesgid){
    $stmt = oci_parse($conn,"insert into topicmessage values($id,$mesgid)");
    oci_execute($stmt);
    $username = $_SESSION['user'];
    $user = new USER();
    $user->makeuser($conn,$username);
    $userid = $user->userid;    
    $desc =$username." Send message to Topic ".$this->name;
    $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$userid,sysdate,'$desc')");
    oci_execute($activity);
    $desc =$username." Send message to Topic ".$this->name;
    $activity = oci_parse($conn,"insert into groupactivity values(act_seq.nextval,$userid,$id,sysdate,'$desc')");
    oci_execute($activity);    
    }
    public function addmsgnot($conn,$id,$user){
        $stmt = oci_parse($conn,"select usersid from topicusers where topic_id=$id");
        oci_execute($stmt);
        while($row=oci_fetch_array($stmt)){
            $userid = $row['USERSID'];
            if(!$this->checknot($conn,$id,$userid)){
                if($user!=$userid){
                    $query = oci_parse($conn,"insert into messagenot(notid,usersid,topic_id) values(msgnot_seq.nextval,$userid,$id)");
                    oci_execute($query);
                }
            }
        }
    }
    public function checknot($conn,$id,$userid){
        $stmt = oci_parse($conn,"select * from messagenot where topic_id=$id and usersid=$userid");
        oci_execute($stmt);
        if($row=oci_fetch_array($stmt)){
            return true;
        }
        else{
            return false;
        }
    }
    public function clearnot($conn,$id,$userid){
        if($this->checknot($conn,$id,$userid)){
            $stmt = oci_parse($conn,"delete from messagenot where topic_id=$id and usersid=$userid");
            oci_execute($stmt);
        }
    }

    
}
class USER
{ 
public $con='';
public $userid='';
public $username='';
public $email='';
public $status;
 public function __construct()
 {
  $database = new Database();
  $db = $database->dbConnection();
  $this->con = $db;
}
 public function makeuser($conn, $username){
     $this->con = $conn;
     $this->username=$username;
     $sql = oci_parse($conn, "select usersid,emailid,status_2 from users where username = '$username'");
     $res = oci_execute($sql);
     $row = oci_fetch_array($sql, OCI_BOTH);
     $this->email = $row['EMAILID'];
     $this->userid = $row['USERSID'];
     $this->status = $row['STATUS_2'];
     
 }
public function addmessage($conn,$userid,$message,$msgid){
    $stmt = oci_parse($conn,"insert into message values($msgid,$userid,'$message',sysdate)");
    oci_execute($stmt);
}
 public function runQuery($sql)
 {
  $stmt = oci_parse($this->con,$sql);
  return $stmt;
 }
 public function uname(){
     return $this->username;
 }
 public function email(){
     return $this->email;
 }
 public function status(){
     return $this->status;
 }
 public function ID()
 {
   return $this->userid;
 }
 public function printUser(){
        echo "$this->userid";
        echo "$this->username";
        echo "$this->email";
        echo "$this->status";
    }
 public function register($uname,$email,$upass,$conn)
 {
  try
  {       
   $sql = oci_parse($conn, "insert into users values(user_seq.nextval,'$uname','$upass','$email',sysdate,0,'',0,'pro.png','bridge.jpg')");
   $res = oci_execute($sql);
   $this->makeuser($conn,$uname);
   return $res;
  }
  catch(PDOException $ex)
  {
   echo $ex->getMessage();
  }
 }
 public function login($row,$conn)
 {
     $this->username = $row['USERNAME'];
     $this->makeuser($conn,$this->username);
     $_SESSION['user'] = $this->username;
     $_SESSSION['status']= $this->status;
     $stmt = oci_parse($conn,"update users set line=1 where usersid =$this->userid");
     oci_execute($stmt);
 } 
 public function is_logged_in()
 {
  if(isset($_SESSION['user']))
  {
   return true;
  }
 }
 public function redirect($url)
 {
  header("Location: $url");
 }
 
 public function logout($conn)
 {
     $stmt = oci_parse($conn,"update users set line=0 where usersid =$this->userid");
     oci_execute($stmt);
  session_destroy();
  $_SESSION['user'] = false;
 }
 
 function send_mail($email,$message,$subject)
 {      
  require_once('\phpmailer\class.phpmailer.php');
  $mail = new PHPMailer();
  $mail->IsSMTP(); 
  $mail->SMTPDebug  = 0;                     
  $mail->SMTPAuth   = true;                  
  $mail->SMTPSecure = "ssl";                 
  $mail->Host       = "smtp.gmail.com";      
  $mail->Port       = 465;             
  $mail->AddAddress($email);
  $mail->Username="ediscuss00@gmail.com";  
  $mail->Password="raat2chalis";            
  $mail->SetFrom('ediscuss00@gmail.com','E-Discussion');
  $mail->AddReplyTo("ediscuss00@gmail.com","E-Discussion");
  $mail->Subject    = $subject;
  $mail->MsgHTML($message);
  $mail->Send();
 } 
}
class friendship{
  public $senderid='';
  public $recieverid='';
  public $status=0;
public function __construct()
 {
  $database = new Database();
  $db = $database->dbConnection();
  $this->con = $db;
}
public function getFriendship($conn,$id){
    $stmt=oci_parse($conn,"select senderid,recieverid,status_2 from friendship where id=$id");
    oci_execute($stmt);
    $row=oci_fetch_array($stmt);
    $this->senderid=$row['SENDERID'];
    $this->recieverid=$row['RECIEVERID'];
    $this->status=$row['STATUS_2'];
    return $this;
}
    public function addfriendship($conn,$id,$senderid,$recieverid){
        $stmt = oci_parse($conn,"insert into friendship values($id,$senderid,$recieverid,sysdate,0)");
        oci_execute($stmt);
        $stmt = oci_parse($conn,"select username from users where usersid=$recieverid");
        oci_execute($stmt);
        $row= oci_fetch_array($stmt);
        $uname =$row['USERNAME'];
        $username = $_SESSION['user'];
        $user = new USER();
        $user->makeuser($conn,$username);
        $userid = $user->userid;    
        $desc =$username." Send Request to ".$uname;
        $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$userid,sysdate,'$desc')");
        oci_execute($activity);
        $user2 = new USER();
        $user2->makeuser($conn,$uname);
        $desc = $username. " Send you a Friend Request";
//        $notification = oci_parse($conn,"insert into notification values(not_seq.nextval,$user2->userid,'$desc',0,sysdate)");
//        oci_execute($notification);
        
    }
    public function acceptfriendship($conn,$id,$userid,$friendid){
        $stmt = oci_parse($conn,"update friendship set status_2=1 where id=$id");
        if(oci_execute($stmt)){
        $stmt = oci_parse($conn,"insert into usersfriend values($userid,$friendid,$id)");
        oci_execute($stmt);
        $stmt = oci_parse($conn,"insert into usersfriend values($friendid,$userid,$id)");
        oci_execute($stmt);
        $stmt = oci_parse($conn,"select username from users where usersid =$friendid");
        oci_execute($stmt);
        $row= oci_fetch_array($stmt);
        $uname = $row['USERNAME'];
        $username = $_SESSION['user'];
        $user = new USER();
        $user->makeuser($conn,$username);
        $userid = $user->userid;    
        $desc =$username." Accept the Friend Request of ".$uname;
        $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$userid,sysdate,'$desc')");
        oci_execute($activity);
        $user2 = new USER();
        $user2->makeuser($conn,$uname);    
//        $desc = $username. " Accepted your Friend Request";
//        $notification = oci_parse($conn,"insert into notification values(not_seq.nextval,$user2->userid,'$desc',0,sysdate)");
        oci_execute($notification);    
        $desc =$username." and ".$uname." are now friends";
        $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$userid,sysdate,'$desc')");
        oci_execute($activity);
        $desc =$uname." and ".$username." are now friends";
        $activity = oci_parse($conn,"insert into activity values(act_seq.nextval,$friendid,sysdate,'$desc')");
        oci_execute($activity);
        }
    }
    public function removefriendship($conn,$id){
        $stmt = oci_parse($conn,"delete from usersfriend where friendship_id=$id");
        oci_execute($stmt);
        $stmt = oci_parse($conn,"delete from friendship where id=$id");
        oci_execute($stmt);
    }
    public function rejectfriendship($conn,$id){
        $stmt = oci_parse($conn,"delete from friendship where id=$id");
        oci_execute($stmt);
    }
}
class newsfeed{
    public $con;
public function __construct()
 {
  $database = new Database();
  $db = $database->dbConnection();
  $this->con = $db;
}
    public function disconnect($conn){
        oci_close($conn);
    }
    public function clearnot($conn,$id){
        $stmt=oci_parse($conn,"update notification set status=1 where usersid=$id");
        oci_execute($stmt);
    }
    public function getTopics($conn,$id){
        $stmt = oci_parse($conn,"select * from topic t where t.id in (select tc.topic_id from topiccategory tc where tc.category =any(select skillname from usersskill where usersid=$id union select intrest from usersinterest where usersid=$id)) or t.id in (select td.topic_id from tags td where td.tagname=any(select skillname from usersskill where usersid=$id union select intrest from usersinterest where usersid=$id)) or t.name =any(select skillname from usersskill where usersid=$id union select intrest from usersinterest where usersid=$id)");
        oci_execute($stmt);
        $result = array();
        $msg=0;
     while(($row=oci_fetch_array($stmt))!=false){
         $id = $row['ID'];
         $name = $row['NAME'];
         $admin = $row['ADMINID'];
         $description = $row['DESCRIPTION'];
         $q=oci_parse($conn,"select username from users where usersid=$admin");
         oci_execute($q);
         $h = oci_fetch_array($q);
         $username = $h['USERNAME'];
         $query = oci_parse($conn,"select count(*) from messagenot where topic_id=$id");
         oci_execute($query);
         $r = oci_fetch_array($query);
         $msg=$r['COUNT(*)'];
 array_push($result,array('id'=>$id,'name'=>$name,'admin'=>$admin,'description'=>$description,'username'=>$username,'msg'=>$msg));
    }
        echo json_encode(array('topic'=>$result));
    }
    public function getrecentactivities($conn,$id){
        $stmt = oci_parse($conn," (select detail,times from activity where usersid in (select friendid from usersfriend where usersid =$id)) union (select detail,times from groupactivity where topicid in (select topic_id from topicusers where usersid=$id)) order by times desc");
        oci_execute($stmt);
        $result = array();
        while($row=oci_fetch_array($stmt)){
            $detail = $row['DETAIL'];
            array_push($result,array('detail'=>$detail));
        }
        echo json_encode(array('ract'=>$result));
    }
}
class thread{
    public $con;
    public function __construct()
 {
  $database = new Database();
  $db = $database->dbConnection();
  $this->con = $db;
}
    public function isthread($conn,$userid,$id){
        $stmt=oci_parse($conn,"select * from thread where (user1id=$userid and user2id=$id) or (user1id=$id and user2id=$userid)");
        oci_execute($stmt);
        if($row=oci_fetch_array($stmt)){
            return true;
        }
        else{
            return false;
        }
    }
    public function makethread($conn,$userid,$id){
        $stmt=oci_parse($conn,"insert into thread values(thread_seq.nextval,$userid,$id,'',sysdate)");
        oci_execute($stmt);
    }
    public function insert($conn,$userid,$id){
        $stmt = oci_parse($conn,"select msg_seq.nextval from dual");
        oci_execute($stmt);
        $row = oci_fetch_array($stmt);
        $msgid =$row['NEXTVAL'];
        $stmt=oci_parse($conn,"insert into message values($msgid,$userid,'this is test msg',sysdate)");
        oci_execute($stmt);
        $stmt = oci_parse($conn,"insert into threadmessage values($id,$msgid)");
        oci_execute($stmt);
    }
    public function getmsg($conn,$id){
        $stmt = oci_parse($conn,"select m.messagetext,m.senderid,u.username,m.time from threadmessage t , message m, users u where t.thread_id=$id and t.message_id=m.id and m.senderid = u.usersid order by m.time");
        oci_execute($stmt);
        $result = array();
        $user=0;
        while($row=oci_fetch_array($stmt)){
            $msg = $row['MESSAGETEXT']->load();
            $username = $row['USERNAME'];
            if($username==$_SESSION['user']){
                $user=1;
            }
            $time = $row['TIME'];
            array_push($result,array('text'=>$msg,'username'=>$username,'time'=>$time,'user'=>$user));
            $user=0;
        }
        echo json_encode(array('msg'=>$result));
    }
     public function addmessage($conn,$userid,$message,$mesgid){
     $stmt = oci_parse($conn,"insert into message values($mesgid,$userid,'$message',sysdate)");
     oci_execute($stmt);
}
     public function addmess($conn,$id,$mesgid){
        $stmt = oci_parse($conn,"insert into threadmessage values($id,$mesgid)");
        oci_execute($stmt);
         $user = new USER();
         $username = $_SESSION['user'];
         $user->makeuser($conn,$username);
         $stmt = oci_parse($conn,"select user1id ,user2id from thread where id=$id");
         oci_execute($stmt);
         $row=oci_fetch_array($stmt);
         $user1 = $row['USER1ID'];
         $user2 = $row['USER2ID'];
         if($user1==$user->userid){
             $friendid=$user2;
         }else{
             $friendid=$user1;
         }
         $stmt = oci_parse($conn,"select username from users where usersid=$friendid");
         oci_execute($stmt);
         $row=oci_fetch_array($stmt);
         $fuser=$row['USERNAME'];
         $desc= $username." send message to ".$fuser;
         $stmt = oci_parse($conn,"insert into activity values(act_seq.nextval,$user->userid,sysdate,'$desc')");
         oci_execute($stmt);
         return $friendid;
     }
    public function updatethread($conn,$id,$message){
        $stmt = oci_parse($conn,"update thread set lastupdate=sysdate where id=$id");
        oci_execute($stmt);
        $stmt = oci_parse($conn,"update thread set lastmessage='$message' where id=$id");
        oci_execute($stmt);
    }

    public function getThreads($conn,$userid){
        $stmt = oci_parse($conn,"select * from thread where user2id=$userid or user1id=$userid order by lastupdate desc");
        oci_execute($stmt);
        $empty=true;
        $result = array();
            while($row = oci_fetch_array($stmt)){
            $msg=0;    
            $empty=false;
            $id = $row['ID'];
            $user1id = $row['USER1ID'];
            $user2id = $row['USER2ID'];
            $message = $row['LASTMESSAGE'];    
            if($user1id==$userid){
                $friendid = $user2id;
            }else{
                $friendid = $user1id;
            }
                $st = oci_parse($conn,"select username from users where usersid=$friendid");
                oci_execute($st);
                $res= oci_fetch_array($st);
                $username = $res['USERNAME'];
                $query = oci_parse($conn,"select * from messagenot where thread_id=$id and usersid=$userid");
                oci_execute($query);
                if($r=oci_fetch_array($query)){
                 $msg=1;   
                }
                if($message==''){
                    $message=' ';
                }
                array_push($result,array('friendid'=>$friendid,'username'=>$username,'id'=>$id,'msg'=>$msg,'message'=>$message));
            }
        if(!$empty)
            echo json_encode(array('thread'=>$result));
        else{
            echo "no";
        }
        }
    public function addmsgnot($conn,$id,$userid){
        if(!$this->checknot($conn,$id,$userid)){
            $query = oci_parse($conn,"insert into messagenot(notid,usersid,thread_id) values(msgnot_seq.nextval,$userid,$id)");
            oci_execute($query);
        }
    }
    public function checknot($conn,$id,$userid){
        $stmt = oci_parse($conn,"select * from messagenot where thread_id=$id and usersid=$userid");
        oci_execute($stmt);
        if($row=oci_fetch_array($stmt)){
            return true;
        }
        else{
            return false;
        }
    }
    public function clearnot($conn,$id,$userid){
        if($this->checknot($conn,$id,$userid)){
            $stmt = oci_parse($conn,"delete from messagenot where thread_id=$id and usersid=$userid");
            oci_execute($stmt);
        }
    }
}    
class profile{
    public $con;
    public function __construct()
 {
  $database = new Database();
  $db = $database->dbConnection();
  $this->con = $db;
}
        public function getActivities($conn,$id){
        $stmt = oci_parse($conn,"select detail,times from activity where usersid=$id order by times desc");
        oci_execute($stmt);
        $result = array();
        while($row=oci_fetch_array($stmt)){
            $detail = $row['DETAIL'];
            $detail = str_replace($_SESSION['user'],"You",$detail);
            $detail = str_replace("has","have",$detail);
            $time = $row['TIMES'];
            array_push($result,array('detail'=>$detail,'time'=>$time));        
        }
        echo json_encode(array('activity'=>$result));
    }
        public function getFriends($conn,$id){
        $stmt = oci_parse($conn,"select u.username,u.usersid,u.line,u.userpic from usersfriend f , users u where f.usersid=$id and f.friendid = u.usersid");
        oci_execute($stmt);
        $online =0;    
        $result = array();
        while($row=oci_fetch_array($stmt)){
            $friendid = $row['USERSID'];
            $username = $row['USERNAME'];
            $online =$row['LINE'];
            $userpic = $row['USERPIC'];
            array_push($result,array('id'=>$friendid,'name'=>$username,'line'=>$online,'userpic'=>$userpic));    
        }
        echo json_encode(array('friends'=>$result));
    }
    public function getTopics($conn,$id){
        $stmt = oci_parse($conn,"select t.name,t.id,us.userpic from topicusers u , topic t , users us where u.usersid=$id and u.topic_id = t.id and t.adminid=us.usersid");
        oci_execute($stmt);
        $user=$id;
        $result = array();
        while($row=oci_fetch_array($stmt)){
            $name = $row['NAME'];
            $id = $row['ID'];
            $img = $row['USERPIC'];
            $query = oci_parse($conn,"select count(*) from messagenot where topic_id=$id and usersid=$user");
             oci_execute($query);
             $r = oci_fetch_array($query);
             $msg=$r['COUNT(*)'];
            array_push($result,array('id'=>$id,'name'=>$name,'msg'=>$msg,'img'=>$img));
        }
        echo json_encode(array('topics'=>$result));
    }
}
Class UserDetails{
public  $FirstName='';
public $LastName='';
public  $gender='';
public  $country='';
public  $dob='';
public $useId;
public function __construct()
{
  $database = new Database();
  $db = $database->dbConnection();
  $this->con = $db;
}
public function insertNumber($conn,$typeid,$num,$user){
    $stmt =oci_parse($conn,"select * from phonenumber where usersid=$user and type_id=$typeid");
    oci_execute($stmt);
    if($row=oci_fetch_array($stmt)){
        $q = oci_parse($conn,"update phonenumber set phonenumber='$num' where usersid=$user and type_id=$typeid");
        oci_execute($q);
    }else{
        $q = oci_parse($conn,"insert into phonenumber values('$num',$user,$typeid)");
        oci_execute($q);
    }
}    
public function insertlink($conn,$typeid,$lin,$user){
    $stmt =oci_parse($conn,"select * from userslinks where usersid=$user and links_id=$typeid");
    oci_execute($stmt);
    if($row=oci_fetch_array($stmt)){
        $q = oci_parse($conn,"update userslinks set link='$lin' where usersid=$user and links_id=$typeid");
        oci_execute($q);
    }else{
        $q = oci_parse($conn,"insert into userslinks values($typeid,$user,'$lin')");
        oci_execute($q);
    }
}    
public function insertInterest($conn,$in,$user){
    $stmt =oci_parse($conn,"select * from Usersinterest where usersid=$user and intrest='$in'");
    oci_execute($stmt);
    if($row=oci_fetch_array($stmt)){
    //    $q = oci_parse($conn,"update phonenumber set phonenumber='$num' where usersid=$user and type_id=$typeid");
    //    oci_execute($q);
    }else{
        $q = oci_parse($conn,"insert into usersinterest values($user,'$in')");
        oci_execute($q);
    }
}
public function insertEducation($conn,$ed,$fr,$t,$user){
    $stmt =oci_parse($conn,"select * from education where usersid=$user and institutename='$ed'");
    oci_execute($stmt);
    if($row=oci_fetch_array($stmt)){
    //    $q = oci_parse($conn,"update phonenumber set phonenumber='$num' where usersid=$user and type_id=$typeid");
    //    oci_execute($q);
    }else{
    $q = oci_parse($conn,"insert into education values($user,'$ed',$fr,$t)");
    oci_execute($q);
    }
}     
public function insertWork($conn,$ed,$fr,$t,$user){
    $stmt =oci_parse($conn,"select * from work where usersid=$user and companyname='$ed'");
    oci_execute($stmt);
    if($row=oci_fetch_array($stmt)){
    //    $q = oci_parse($conn,"update phonenumber set phonenumber='$num' where usersid=$user and type_id=$typeid");
    //    oci_execute($q);
    }else{
    $q = oci_parse($conn,"insert into work values($user,'$ed',$fr,$t)");
    oci_execute($q);
    }
}     
public function insertSkill($conn,$in,$user){
    $stmt =oci_parse($conn,"select * from UsersSkill where usersid=$user and skillname='$in'");
    oci_execute($stmt);
    if($row=oci_fetch_array($stmt)){
    //    $q = oci_parse($conn,"update phonenumber set phonenumber='$num' where usersid=$user and type_id=$typeid");
    //    oci_execute($q);
    }else{
        $q = oci_parse($conn,"insert into usersSkill values($user,'$in')");
        oci_execute($q);
    }
}    
public function saveDetails($con,$userId,$Fname,$Lname,$gen,$country,$udate){
  $this->useId=$userId;      
  $this->FirstName=$Fname ;
  $this->LastName=$Lname;
  $this->gender=$gen;
  $this->country=$country;
  $this->dob=$udate;

$sql = oci_parse($con, "Select * from userdetails where usersid=$userId");
$res = oci_execute($sql);    
$row = oci_fetch_array($sql);
if(!$row){     
   $sql = oci_parse($con, "insert into userDetails values($userId,'$Fname','$Lname','$gen','$country',to_date('$udate','DD/MM/YYYY'))");
   $res = oci_execute($sql);    
}
else{
	$sql = oci_parse($con, "update userDetails set first_name='$Fname',last_name='$Lname',gender='$gen',country='$country',DOB=to_date('$udate','DD/MM/YYYY') where usersid=$userId") ;
   $res = oci_execute($sql);    
	
}
}
}
