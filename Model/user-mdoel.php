<?php
namespace Model;

include_once "../Configuration/database-connection.php";

use DbConnection;

class User
{
  private $DBconn;

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

  /* 
  Dependency injection to use Database connection class properties
  */
  public function __construct(DbConnection $DBconn)
  {
    $this->DBconn = $DBconn;
  }

  /**
   * @return array 
   * gets all data from user tablee
   */

  public function get(): array
  {
    try {
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
      return array("error" => $e->getMessage());
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
      return array("error" => $e->getMessage());
    }
  }

  /**
   * Creates new user / Inserts into user table
   */

  public function create($data)
  {
    try {
      if (!User::isJson($data)) {
        throw new \Exception("Not json data");

      } else {
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
      return $result;

    } catch (\Exception $e) {

      return array("error" => $e->getMessage());
    }

  }

}

?>