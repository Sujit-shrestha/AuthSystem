<?php 
namespace RequestHandlers;
use Model\Category;
use Configg\DBConnect;
use Validate\Validator;

class CategoryRequestHandlers
{
  public static function createCategory(){
    $categoryObj = new Category(new DBConnect());
    $jsonData = file_get_contents('php://input');
    $decodedData = json_decode($jsonData, true);
    $keys = [
      'category_name' =>['empty'],
      'parent' => ['empty'],
    ];

    $validationResult = Validator::validate($decodedData, $keys);

    if(!$validationResult["validate"]){
      return [
        "status" =>"false",
        "statusCode" => "409",
        "message" => $validationResult,
        "data"=>json_decode($jsonData, true)
      ];
    }
 
    //checking in database
    $checkIfCategoryExists = $categoryObj->get($decodedData["category_name"] , NULL);
   
    if($checkIfCategoryExists["status"] === "true"){
      return [
        "status" => "false",
        "statusCode" => 403,
        "message" =>"Category alredy exists",
        "data" =>[]
      ];
    }
   
    $response = $categoryObj->create($jsonData);

    if($response ["status"] === "false")
    {
      throw new \Exception("Unalble to create in database.");
    }
    return [
      "status"=>"true",
      "statusCode" => 200,
      "message" => "Category created succsessfully!!",
      "data" => json_decode($jsonData, true)

    ];


  }

  public static function getCategory(){

  }

  public static function updateCategory(){

  }

  public static function deleteCategory(){
  }
}


?>