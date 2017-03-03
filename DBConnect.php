<?php
class Database{
    private $host = "localhost";
    private $db_name = "dbtest";
    private $username = "ahsan4";
    private $password = "ahsansam";
    public $conn;
public function dbConnection(){ 
 $this->conn = null;    
 try{
      $this->conn = oci_connect($this->username, $this->password, "localhost/orcl");
 }
  catch(PDOException $exception){
         echo "Connection error: " . $exception->getMessage();
 }
         
 return $this->conn;
}
public function getCategories($conn){
    $stmt = oci_parse($conn,"select category from topiccategory");
    oci_execute($stmt);
    $result =array();
    while($row = oci_fetch_array($stmt)){
        $categories  = $row['CATEGORY'];
        array_push($result,array('cat'=>$categories));
    }
    echo json_encode(array('cat'=>$result));
}
public function gettags($conn){
    $stmt = oci_parse($conn,"select name from tags");
    oci_execute($stmt);
    $result =array();
    while($row = oci_fetch_array($stmt)){
        $categories  = $row['NAME'];
        array_push($result,array('tag'=>$categories));
    }
    echo json_encode(array('tag'=>$result));
}
public function getskill($conn){
    $stmt = oci_parse($conn,"select skillname from usersskill");
    oci_execute($stmt);
    $result =array();
    while($row = oci_fetch_array($stmt)){
        $categories  = $row['SKILLNAME'];
        array_push($result,array('skill'=>$categories));
    }
    echo json_encode(array('skill'=>$result));
}
public function getinterest($conn){
    $stmt = oci_parse($conn,"select interest from usersinterest");
    oci_execute($stmt);
    $result =array();
    while($row = oci_fetch_array($stmt)){
        $categories  = $row['INTEREST'];
        array_push($result,array('interest'=>$categories));
    }
    echo json_encode(array('interest'=>$result));
}
    public function geteducation($conn){
    $stmt = oci_parse($conn,"select institutename from education");
    oci_execute($stmt);
    $result =array();
    while($row = oci_fetch_array($stmt)){
        $categories  = $row['INSTITUTENAME'];
        array_push($result,array('education'=>$categories));
    }
    echo json_encode(array('education'=>$result));
}
    public function getinstitute($conn){
    $stmt = oci_parse($conn,"select companyname from work");
    oci_execute($stmt);
    $result =array();
    while($row = oci_fetch_array($stmt)){
        $categories  = $row['COMPANYNAME'];
        array_push($result,array('institute'=>$categories));
    }
    echo json_encode(array('institute'=>$result));
}
}
?>
