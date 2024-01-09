<?php
namespace Configg;

use Exception;
use mysqli;

class DBConnect
{

  private $hostname = "localhost:3000";
  private $username = "root";
  private $password = "";
  private $database = "api_database";

  public $conn;

  //begins connection on object instantiation
  public function __construct()
  {
    $this->connectToDatabase();
  }

  public function connectToDatabase()
  {
    try {
      $this->conn = new mysqli($this->hostname, $this->username, $this->password, $this->database);

      if ($this->conn->connect_error) {
        throw new Exception($this->conn->connect_error);
      } 
    
    } catch (Exception $e) {
      echo "\n" . $e->getMessage() . "\n";
    }

  }

  public function disconnectFromDatabase(){
    try{
      if($this->conn){
        $this->conn->close();
      }else{
        throw new Exception("Unable to disconnect from database.");
      }
      
    }catch (Exception $e) {
      echo  $e->getMessage();
    }
  }
}

?>