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
  gets all data from user tablee
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

//updates the database using id as reference
  public function update($id , $data){
    try{
      $data = json_decode($data,true);
      $sql = "UPDATE User 
      SET email = '$data[email]' ,
          password = '$data[password]' ,
          username = '$data[username]' ,
          name = '$data[name]',
          address = '$data[address]',
          user_type = '$data[user_type]'
      WHERE id = '$id'
       ";
      $result = $this->DBconn->conn->query($sql);
      return $result;
    
    }catch (\Exception $e){
        return array("error" => $e->getMessage()); 
    }
  }

  public function create(){
    return "create method";
  }
  public function delete(){
    return "delete method";
  }
  
}

?>