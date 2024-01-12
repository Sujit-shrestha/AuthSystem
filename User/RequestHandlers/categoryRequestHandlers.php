<?php
namespace RequestHandlers;

use Exception;
use Model\Category;
use Configg\DBConnect;
use Validate\Validator;

class CategoryRequestHandlers
{
  /**
   * creates category
   */
  public static function createCategory()
  {
    $categoryObj = new Category(new DBConnect());
    $jsonData = file_get_contents('php://input');
    $decodedData = json_decode($jsonData, true);
    $keys = [
      'category_name' => ['empty'],
      'parent' => ['empty'],
    ];

    $validationResult = Validator::validate($decodedData, $keys);

    if (!$validationResult["validate"]) {
      return [
        "status" => "false",
        "statusCode" => "409",
        "message" => $validationResult,
        "data" => json_decode($jsonData, true)
      ];
    }

    //checking in database
    $checkIfCategoryExists = $categoryObj->get($decodedData["category_name"], NULL);

    if ($checkIfCategoryExists["status"] === "true") {
      return [
        "status" => "false",
        "statusCode" => 403,
        "message" => "Category alredy exists",
        "data" => []
      ];
    }

    $response = $categoryObj->create($jsonData);

    if ($response["status"] === "false") {
      throw new Exception("Unalble to create in database.");
    }
    return [
      "status" => "true",
      "statusCode" => 200,
      "message" => "Category created succsessfully!!",
      "data" => json_decode($jsonData, true)

    ];


  }

  /**
   * gets all category in bulk
   */
  public static function getAll()
  {
    $categoryObj = new Category(new DBConnect());
    $response = $categoryObj->getAll();



    return [
      "statusCode" => 200,
      "status" => $response["status"],
      "message" => $response["message"],
      "data" => $response["data"]
    ];
  }

  /**
   * Gets category by the name of parent 
   */
  public static function getByParent()
  {

    $categoryModelObj = new Category(new DBConnect());
    $parent = $_GET["parent"];
    $response = $categoryModelObj->get(NULL, $parent);

    return [
      "status" => $response["status"],
      "statusCode" => 200,
      "message" => $response["message"],
      "data" => $response["data"]
    ];



  }

  /**
   *  takes preParent from params and newParent name from 
   *  body as json value
   */
  public static function updateParent():array
  {
    try {
      $categoryModelObj = new Category(new DBConnect());

      $jsonData = file_get_contents("php://input");
      $decodedData = json_decode($jsonData, true);
      $previousParent = $_GET["previousParent"];
      if(empty($previousParent)){
        throw new Exception("Previous parent not provided!!");
      }
      $result = $categoryModelObj->get(NULL, $previousParent);

     if($result["status"]=="false"){
      throw new Exception("Parent category not found in database!!");
     }
      
      //validation
      $dataToValidate = [
        "previousParent" => $previousParent,
        "newParent" => $decodedData["newParent"],
      ];
      $keys = [
        'newParent' => ['empty', 'required'],
        'previousParent' => ['empty' ,'required']
      ];
      
      $validationResult = Validator::validate($dataToValidate, $keys);
      if (!$validationResult["validate"]) {
        $response = array(
          "status" => "false",
          "statusCode" => "409",
          "message" => $validationResult,
          "data" => $dataToValidate
        );
        return $response;
      }
      
      $response = $categoryModelObj->updateParent($_GET["previousParent"], $decodedData["newParent"]);

      if (!$response["status"]) {
        throw new Exception("Unalbe to update in database!!");
      }
      return [
        "status" => $response["status"],
        "statusCode" => 200,
        "message" => $response["message"]
      ];

    }catch(Exception$e){
      return [
          "status" => "false",
          "message" => $e->getMessage()
      ];
    }
  }


  public function updateChild(){
    try {
      $categoryModelObj = new Category(new DBConnect());

      $jsonData = file_get_contents("php://input");
      $decodedData = json_decode($jsonData, true);
      $previousChild = $_GET["previousChild"];
      if(empty($previousChild)){
        throw new Exception("Previous child not provided!!");
      }
      $result = $categoryModelObj->get($previousChild ,NULL);

     if($result["status"]=="false"){
      throw new Exception("Child category not found in database!!");
     }
      
      //validation
      $dataToValidate = [
        "previousChild" => $previousChild,
        "newChild" => $decodedData["newChild"],
      ];
      $keys = [
        'newChild' => ['empty', 'required'],
        'previousChild' => ['empty' ,'required']
      ];
      
      $validationResult = Validator::validate($dataToValidate, $keys);
      if (!$validationResult["validate"]) {
        $response = array(
          "status" => "false",
          "statusCode" => "409",
          "message" => $validationResult,
          "data" => $dataToValidate
        );
        return $response;
      }
      
      $response = $categoryModelObj->updateCategory($previousChild, $decodedData["newChild"]);

      if (!$response["status"]) {
        throw new Exception("Unalbe to update in database!!");
      }
      return [
        "status" => $response["status"],
        "statusCode" => 200,
        "message" => $response["message"]
      ];

    }catch(Exception$e){
      return [
          "status" => "false",
          "message" => $e->getMessage(),
          "statusCode" =>500
      ];
    }
  }

  public static function deleteCategory()
  {
  }
}


?>