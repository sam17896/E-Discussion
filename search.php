<?php
include_once 'DBConnect.php';
include_once 'classes.php';
  $db = new Database();
    $conn = $db->dbConnection(); 
    $user = new USER();
    
     
if($_POST)
{
    $q=$_POST['search'];
/*
$sql_res=mysql_query("select  Username from users where  Username like '%$q%'");
*/
$stmt = $user->runQuery("select usersid,Username,UserPic from users where  Username like '%$q%'");
oci_execute($stmt);

while($row = oci_fetch_array($stmt))
{
$username=$row['USERNAME'];
$userimg=$row['USERPIC'];
$id = $row['USERSID'];
$b_username='<strong>'.$q.'</strong>';
$b_email='<strong>'.$q.'</strong>';
$final_username = str_ireplace($q, $b_username, $username);

?>
<div class="show" >
    <a href='profile.php?id=<?php echo $id ?>'><img src="uploaded_files/<?php echo $userimg ?>" style="width:50px; height:50px; margin-right:6px;" /><span class="name" style="color: #009688;"><?php echo $final_username; ?></span>&nbsp;</a><br/><br/>
</div>
<?php
}
$stmt = $user->runQuery("select t.id,t.name,u.Username,u.UserPic from users u, topic t where  t.name like '%$q%' and u.usersid = t.adminid");
oci_execute($stmt);

while($row = oci_fetch_array($stmt))
{
$username=$row['NAME'];
$userimg=$row['USERPIC'];
$id = $row['ID'];
$b_username='<strong>'.$q.'</strong>';
$b_email='<strong>'.$q.'</strong>';
$final_username = str_ireplace($q, $b_username, $username);

?>
<div class="show" >
    <a href='topic.php?id=<?php echo $id ?>'><img src="imgs/group.png" style="width:50px; height:50px; margin-right:6px;" /><span class="name" style="color: #009688;"><?php echo $final_username; ?></span>&nbsp;</a><br/><br/>
</div>
<?php
}
    
}
?>