<?php
namespace RequestHandlers;

require_once "./Configuration/config.php";
require_once "./Middleware/authentication.php";
require_once "./Configuration/database-connection.php";
require_once "./Model/user-mdoel.php";
require_once "./Configuration/session.php";
require_once "./Routes/login.php";
require_once "./Middleware/response.php";
require_once "./Middleware/response.php";
require_once "./Validate/validator.php";

use Exception;
use Middleware\JWTTokenHandlerAndAuthentication;
use Configg\DBConnect;
use Model\User;

use Validate\Validator;

class RequestHandlers
{
  public static function respondWithJson($data, $status)
  {
    header('Content-type : application/json');
    http_response_code($status);
    echo json_encode($data);
  }
  /** 
   * takes auth  ,verifies , gives  response
   */
  public static function getByIdOrUsername()
  {
    $authToken = $_SERVER["HTTP_AUTHORIZATION"];
    $authToken = explode(" ", $authToken);
    $authToken = $authToken[1];
    $userObj = new User(new DBConnect());
    $authenticationObj = new JWTTokenHandlerAndAuthentication($userObj);
    $tokenAuthStatus = JWTTokenHandlerAndAuthentication::verifyToken($authToken);
    if ($tokenAuthStatus) {
      $id = $_GET["id"] ?? NULL;
      $username = $_GET["username"] ?? NULL;
      $result = $userObj->get($id, $username);
      unset($result["password"]);

      return [
        "status" => "true",
        "statusCode" => "200",
        "message" => "Data extraceted.",
        "data" => $result
      ];

    } else {
      return [
        "status" => "false",
        "statusCode" => "401",
        "message" => "Unauthorised to get."
      ];
    }
  }
  public static function createUser()
  {
    //creating user so auth not required

    $userObj = new User(new DBConnect());
    $jsonData = file_get_contents('php://input');
    $decodedData = json_decode($jsonData, true);

    //to validatte in the keys
    $keys = [
      'username' => ['empty', 'maxlength', 'format'],
      'password' => ['empty', 'maxlength', 'minLength'],
      'email' => ['empty', 'email'],
      'name' => ['empty'],
      'address' => ['empty'],
      'user_type' => ['empty']
    ];

    $validationResult = Validator::validate($decodedData, $keys);
    if (!$validationResult["validate"]) {
      $response = array(
        "status" => "false",
        "statusCode" => "409",
        "message" => $validationResult,
        "data" => json_decode($jsonData, true)
      );
      return $response;

    }

    $checkIfUsernameExists = $userObj->get(NULL, $decodedData["username"]);
    if (isset($checkIfUsernameExists["id"])) {
      unset($checkIfUsernameExists);
      return [
        "status" => "false",
        "statusCode" => "409",
        "message" => "Username already exists",
        "data" => $decodedData
      ];
    }
    $result = $userObj->create($jsonData);

    $fetchUserId = $userObj->get(NULL, $decodedData["username"]);
    $userId = $fetchUserId["id"];
    unset($fetchUserId);

    $decodedData["id"] = $userId;

    if ($result) {
      return [
        "status" => "true",
        "statusCode" => "201",
        "message" => "User created successfully",
        "data" => $decodedData
      ];

    } else {
      return [
        "status" => "false",
        "statusCode" => "409",
        "message" => "Unable to create user",
        "data" => json_decode($jsonData, true)
      ];


    }
  }

  public static function updateUser()
  {

    $authToken = $_SERVER["HTTP_AUTHORIZATION"];
    $authToken = explode(" ", $authToken);
    $authToken = $authToken[1];
    $userObj = new User(new DBConnect());
    $authenticationObj = new JWTTokenHandlerAndAuthentication($userObj);

    $tokenAuthStatus = JWTTokenHandlerAndAuthentication::verifyToken($authToken);
    if ($tokenAuthStatus) {

      $jsonData = file_get_contents('php://input');
      $id = $_GET["id"];

      $updateStatus = $userObj->update($id, $jsonData);

      if ($updateStatus["result"] == true) {

        return [
          "status" => "true",
          "statusCode" => "201",
          "message" => "User Updated successfully",
          "updatedData" => json_decode($jsonData)
        ];


      } else {
        return [
          "status" => "false",
          "statusCode" => 409,
          "data" => $updateStatus
        ];
      }
    }
    //disconnecting from database
    $userObj->DBconn->disconnectFromDatabase();
  }
  public static function deleteUser()
  {
    $authToken = $_SERVER["HTTP_AUTHORIZATION"];
    $authToken = explode(" ", $authToken);
    $authToken = $authToken[1];
    $userObj = new User(new DBConnect());
    $authenticationObj = new JWTTokenHandlerAndAuthentication($userObj);

    $tokenAuthStatus = JWTTokenHandlerAndAuthentication::verifyToken($authToken);

    if ($tokenAuthStatus) {
      $id = $_GET["id"];
      $deleteStatus = $userObj->delete($id);

      if ($deleteStatus == true) {
        return [
          "status" => "true",
          "statusCode" => 200,
          "message" => "User if Id :$id deleted successfully"
        ];

      } else {
        return [
          "status" => "false",
          "statusCode" => 400,
          "message" => "$deleteStatus"
        ];

      }

    }

    //disconnecting from database
    $userObj->DBconn->disconnectFromDatabase();

  }
  public static function getBrearerToken()
  {
    try {
      $authToken = $_SERVER["HTTP_AUTHORIZATION"];
      $authToken = explode(" ", $authToken);

      if (!$authToken[0] == "Bearer") {

        throw new Exception("Not a bearer toekn.");
      } else {
        $authToken = $authToken[1];
        return $authToken;
      }

    } catch (Exception $e) {
      return $e->getMessage();
    }


  }
  public static function getUserTypeFromToken()
  {
    try {
      $authToken = self::getBrearerToken();

      return JWTTokenHandlerAndAuthentication::getSpecificValueFromToken($authToken, "user_type");
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
}



?>