<?php
namespace Model;

include_once "../Configuration/database-connection.php";

use Configg\DBConnect;

class User
{
  public $DBconn;
  /* 
    Dependency injection to use Database connection class properties
    */
  public function __construct(DbConnect $DBconn)
  {
    $this->DBconn = $DBconn;
  }

  /**
   * static funciton to check if data is JSON data
   * @ param json_data
   * @return bool
   */

  public static function isJSON(string $jsonData)
  {
    json_decode($jsonData);
    return (json_last_error() == JSON_ERROR_NONE);
  }



  /**
   * @return array 
   * gets all data from user tablee
   */

  public function get(?int $id, ?string $username): array
  {
    try {
      if (isset($id)) {

        $sql = "SELECT * FROM User where id = $id";
        $result = $this->DBconn->conn->query($sql);
        if (!$result) {
          throw new \Exception("Unable to fetch the given id data");
        } else {
          return $result->fetch_assoc();
        }
      }

      if (isset($username)) {

        $sql = "SELECT * FROM User where username = '$username'";
        $result = $this->DBconn->conn->query($sql);

        if ($result->num_rows == 0) {
          throw new \Exception("Unable to fetch the given username data");
        } else {
          return $result->fetch_assoc();
        }
      }




      $sql = "SELECT * FROM User";

      $result = $this->DBconn->conn->query($sql);

      $data = $result->fetch_all(MYSQLI_ASSOC);
      echo json_encode($data);

      if (empty($data)) {
        throw new \Exception("Unable to fetch data form DB");
      } else {
        return $data;
      }

    } catch (\Exception $e) {
      echo $e->getMessage();
      return array(
        "status" => false,
        "error" => $e->getMessage()
      );
    }
  }
  /**
   * updates the database using id as reference
   * 
   */

  public function update(int $id, string $data): array
  {
    try {
      
      if (!User::isJson($data)) {
        throw new \Exception("The data is not json data.");
      } else {
        
        $data = json_decode($data, true);
        $data["password"] = password_hash($data["password"], PASSWORD_BCRYPT);
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
        return array("result" => $result);
      }

    } catch (\Exception $e) {
      print_r(array("error" => $e->getMessage()));
      return array("error" => $e->getMessage());
    }
  }

  /**
   * Creates new user / Inserts into user table
   * @param //jsondata
   * @return bool|array
   */

  public function create($data)
  {
    try {
      if (!User::isJson($data)) {
        throw new \Exception("Not json data");

      } else {
        $data = json_decode($data, true);
        //hashing the inserted password
        $data["password"] = password_hash($data["password"], PASSWORD_BCRYPT);
        $sql = "
        INSERT INTO User 
      (email , password , username,  name , address , user_type)
      VALUES
      ('$data[email]' , '$data[password]' ,'$data[username]' ,'$data[name]' ,'$data[address]' ,'$data[user_type]')
      ";

        $result = $this->DBconn->conn->query($sql);
        return $result;
      }
    } catch (\Exception $e) {
      return array("error" => $e->getMessage());
    }
  }
  /**
   * deletes a user using id
   * @param int id
   * @ return bool || array
   * 
   */
  public function delete(int $id)
  {
    try {
      $sql = "
      DELETE FROM User 
      WHERE id = '$id'
      ";
      $result = $this->DBconn->conn->query($sql);
      echo $result;
      return $result;

    } catch (\Exception $e) {

      return array("error" => $e->getMessage());
    }

  }

}

?>