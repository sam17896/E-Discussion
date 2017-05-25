<?php
class Database{
    private $host = "ec2-23-23-111-171.compute-1.amazonaws.com";
    private $db_name = "daq13v617rddt5";
    private $username = "ofntbuxbxbbnnu";
    private $password = "406b3e2d20dec3cad4a58b0cb16296012887d918e1f3996fc18453164cb459ed";
    public $conn;
    
public function dbConnection(){ 
 $this->conn = null;    
 try{
    //  $this->conn = mysqli_connect($this->host ,$this->username, $this->password, $this->db_name);
     $this->conn = pg_connect("host=ec2-23-23-111-171.compute-1.amazonaws.com port=5432 dbname=daq13v617rddt5 user=ofntbuxbxbbnnu password=406b3e2d20dec3cad4a58b0cb16296012887d918e1f3996fc18453164cb459ed");
 }
  catch(PDOException $exception){
         echo "Connection error: " . $exception->getMessage();
 }
    if(!$this->conn){
        echo 'Cant connect to db';
    }
         
 return $this->conn;
}
public function getCategories($conn){
    $stmt = pg_query($conn,"select category from topiccategory");
    pg_fetch_array($stmt);
    $result =array();
    while($row = pg_fetch_array($stmt)){
        $categories  = $row['CATEGORY'];
        array_push($result,array('cat'=>$categories));
    }
    echo json_encode(array('cat'=>$result));
}
public function gettags($conn){
    $stmt = pg_query($conn,"select name from tags");
    pg_fetch_array($stmt);
    $result =array();
    while($row = pg_fetch_array($stmt)){
        $categories  = $row['NAME'];
        array_push($result,array('tag'=>$categories));
    }
    echo json_encode(array('tag'=>$result));
}
public function getskill($conn){
    $stmt = pg_query($conn,"select skillname from usersskill");
    pg_fetch_array($stmt);
    $result =array();
    while($row = pg_fetch_array($stmt)){
        $categories  = $row['SKILLNAME'];
        array_push($result,array('skill'=>$categories));
    }
    echo json_encode(array('skill'=>$result));
}
public function getinterest($conn){
    $stmt = pg_query($conn,"select interest from usersinterest");
    pg_fetch_array($stmt);
    $result =array();
    while($row = pg_fetch_array($stmt)){
        $categories  = $row['INTEREST'];
        array_push($result,array('interest'=>$categories));
    }
    echo json_encode(array('interest'=>$result));
}
    public function geteducation($conn){
    $stmt = pg_query($conn,"select institutename from education");
    pg_fetch_array($stmt);
    $result =array();
    while($row = pg_fetch_array($stmt)){
        $categories  = $row['INSTITUTENAME'];
        array_push($result,array('education'=>$categories));
    }
    echo json_encode(array('education'=>$result));
}
    public function getinstitute($conn){
    $stmt = pg_query($conn,"select companyname from work");
    pg_fetch_array($stmt);
    $result =array();
    while($row = pg_fetch_array($stmt)){
        $categories  = $row['COMPANYNAME'];
        array_push($result,array('institute'=>$categories));
    }
    echo json_encode(array('institute'=>$result));
}
}
?>
