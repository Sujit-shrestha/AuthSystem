<?php

namespace Model;

include_once "../Configuration/database-connection.php";

use Configg\DBConnect;

class Category
{
  public $DBconn;

  public function __construct(DBConnect $DBconn)
  {
    $this->DBconn = $DBconn;
  }

  /**
   * @param  string
   * @return  array
   * 
   */
  public function get(?string $category_name, ?string $parent):array
  {
    try {

      if (!isset($category_name) && !isset($parent)) {
        throw new \Exception("Category name or parent name cannot be empty!!");
      }

      ///to get data based on category_name only
      if (isset($category_name)) {
        $sql = "SELECT * FROM category WHERE category_name = '$category_name'";
      
        $result = $this->DBconn->conn->query($sql);
       
       

        if (!$result->num_rows > 0) {
          throw new \Exception("Unable to fetch the given id data");
        } else {
          return [
            "status" => "true",
            "message" => "Data extracted successfully!!",
            "data" => $result->fetch_assoc()
          ];
        }
      }

      //extracts data based on parent name only
      if (isset($parent)) {
        $sql = "SELECT * FROM category WHERE parent = '$parent'";
        $result = $this->DBconn->conn->query($sql);
      
        if (!$result->num_rows > 0) {
          throw new \Exception("Unable ot find the parent category!!");
        } else {
          return [
            "status" => "true",
            "message" => "Parent data extracted successfully!!",
            "data" => $result->fetch_assoc()
          ];
        }
      }
      throw new \Exception("unknown error in getting category");
    } catch (\Exception $e) {
      return [
        "status" => "false",
        "message" => $e->getMessage(),
        "data" => []
      ];
    }
  }

  public function update(int $id, array $data)
  {
    try {
      $sql = "UPDATE categories 
        SET name = '$data[name]',
            parent = '$data[parent]'
        WHERE id ='$id'
      ";
      $result = $this->DBconn->conn->query($sql);
      return [
        "status" => "true",
        "message" => "Data updated successfully."
      ];
    } catch (\Exception $e) {
      return [
        "status" => "false",
        "message" => $e->getMessage(),
      ];
    }
  }

  public function delete(int $id)
  {
    try {
      $sql = "
      DELETE FROM categories
      WHERE id = '$id'
      ";
      $result = $this->DBconn->conn->query($sql);

      return [
        "status" => "true",
        "message" => "Category deleted successfully.",
      ];

    } catch (\Exception $e) {
      return [
        "status" => "false",
        "message" => $e->getMessage(),
      ];

    }
  }

  public function create($data)
  {
    try{
      
      $data = json_decode($data, true);
    $sql = "
    INSERT INTO category
    (category_name , parent)
    VALUES 
    ('$data[category_name]' , '$data[parent]')
    ";
    
    $result = $this->DBconn->conn->query($sql);
    if(!$result){
      throw new \Exception("Could not insert into database!!");
    }
    return [
      "status" => "true",
      "message" => "Category created successfully.",
    ];
    }catch (\Exception $e) {  
      return [
        "status"=> "false",
        "message"=> $e->getMessage()
      ];
    }
    
  }
}

?>