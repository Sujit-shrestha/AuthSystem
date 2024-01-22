<?php

namespace RequestHandlers;

use Exception;
use Validate\Validator;
use Model\Location;
use Configg\DBConnect;


class LocationRequestHandlers
{
  /**
   * @return array
   * Takes data as json , validates , checks if already exists and Creates location in database
   */
  public static function createLocation(): array
  {
    $locationObj = new Location(new DBConnect());
    $jsonData = file_get_contents('php://input');
    $decodedData = json_decode($jsonData, true);

    $keys = [
      'location' => ['empty']
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

    $checkIfLocationExists = $locationObj->get($decodedData["location"]);
    if ($checkIfLocationExists["status"] === "true") {
      return [
        "status" => "false",
        "statusCode" => 403,
        "message" => "Location alredy exists",
        "data" => []
      ];
    }
    $response = $locationObj->create($jsonData);

    if ($response["status"] === "false") {
      return [
        "status" => "false",
        "statusCode" => 403,
        "message" => "Unalble to create in database.",
        "data" => []
      ];
    }
    return [
      "status" => "true",
      "statusCode" => 200,
      "message" => "Locaiton created succsessfully!!",
      "data" => $decodedData
    ];
  }

  /**
   * @return  array
   * gets all the avaliable locations in database 
   */
  public static function getAllLocation():array{
    try{
      $locationObj = new Location(new DBConnect());
      $response = $locationObj->getAll();
      if(!$response['status']){
        throw new Exception("Unable to fetch from database!!");
      }

      return [
        "status" => "true",
        "statusCode" => 200,
        "message" => $response['message'],
        "data" => $response['data']
      ];

    }catch(Exception $e){
      return [
        "status" => "false",
        "statusCode" => 404,
        "message" => $e->getMessage() ,
        "data" => []
      ];
    }
  }

  public static function updateLocation(){
    try{
      $locationObj = new Location(new DBConnect());
      $jsonData = file_get_contents("php://input");
      $decodedData = json_decode($jsonData , true);
     
      //validation
      $keys = [
        "previousLocation" => ['empty', 'required'],
        "newLocation" => ['empty' , 'required']
      ];

      $validationResult = Validator::validate($decodedData , $keys);

     
      if(!$validationResult["validate"]){
        return  [
          "status" => "false",
          "statusCode" => "409",
          "message"=> $validationResult,
          "data" => $decodedData
        ];
      }

      //check if is present in database
      $result = $locationObj->get($decodedData["previousLocation"]);

      if ($result["status"] == "false") {
        throw new Exception("Location not found in database to update!!");
      }

      $response = $locationObj->updateLocation($decodedData);
      
      if (!$response["status"]) {
        throw new Exception("Unalbe to update in database!!");
      }
      return [
        "status" => $response["status"],
        "statusCode" => 200,
        "message" => $response["message"],
        "data" => $decodedData
      ];

    }catch(Exception $e){
      return [
        "status" => "false",
        "statusCode" => 409,
        "message" => $e->getMessage() ,
        "data" => $decodedData
      ];
    }
  }

  public static function deleteLocation():array{
    try{
      $locationObj = new Location(new DBConnect());
      $jsonData = file_get_contents("php://input");
      $decodedData = json_decode($jsonData , true);
     
      //validation
      $keys = [
        "location" => ['empty' , 'required']
      ];
      $validationResult = Validator::validate($decodedData, $keys);
      if (!$validationResult["validate"]) {
        return [
          "status" => "false",
          "statusCode" => "409",
          "message" => $validationResult,
          "data" => $decodedData
        ];
      }
      //check if its in database
      //check if is present in database
      $result = $locationObj->get($decodedData["location"]);

      if ($result["status"] == "false") {
        throw new Exception("Location not found in database to delete!!");
      }
      
      $response = $locationObj->deleteLocation($decodedData);
      if(!$response["status"]){
        return [
          "status" => $response["status"],
          "message" => $response["message"],
          "statusCode" => 500
        ];
      }
      return [
        "status" => $response["status"],
        "statusCode" => 200,
        "message" => "Location deleted successfully",
        "data" => $decodedData
      ];
      
    }catch(Exception $e){
      return [
        "status" => "false",
        "statusCode" => 409,
        "message" => $e->getMessage() ,
        "data" =>$decodedData
      ];
    }
  }


}
