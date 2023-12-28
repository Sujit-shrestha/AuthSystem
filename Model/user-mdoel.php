<?php 
namespace Model;
include_once "../Configuration/database-connection.php";

use DbConnection;

class User{
  private $DBconn;

  /* 
  Dependency injection to use Database connection class properties
  */
  public function __construct(DbConnection $DBconn){
    $this->DBconn  = $DBconn;
  }

  /*
  @returns array 
  gets all data from user table
  */
  public function get():array{
    try{
      $sql = "SELECT * FROM User";     

      $result = $this->DBconn->conn->query($sql);

      $data = $result ->fetch_all(MYSQLI_ASSOC);
      echo json_encode($data);

      if(empty($data)){
        throw new \Exception("Unable to fetch data form DB");
      }else{
        return $data;
      }
   
  }catch (\Exception $e){
    return array("error"=> $e->getMessage());
  }
}

  public function updatw(){
    return "update method";
  }

  public function create(){
    return "create method";
  }
  public function delete(){
    return "delete method";
  }
  
}

?>