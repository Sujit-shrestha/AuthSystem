<?php
namespace RequestHandlers;

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

    $authToken = self::getBrearerToken();

    $userObj = new User(new DBConnect());
    $authenticationObj = new JWTTokenHandlerAndAuthentication($userObj);
    $tokenAuthStatus = JWTTokenHandlerAndAuthentication::verifyToken($authToken);

    if ($tokenAuthStatus) {
      $id = $_GET["id"] ?? NULL;
      $username = $_GET["username"] ?? NULL;

      if ($id == NULL && $username == NULL) {
        return [
          "status" => "false",
          "statusCode" => "401",
          "message" => "Id or username must be provided!!"
        ];
      }

      $result = $userObj->get($id, $username);
      if ($result["status"] == "false") {
        return [
          "status" => "false",
          "statusCode" => 404,
          "message" => "User requested not available!!"
        ];
      }
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

    //explicitly assignning employee as user_type so that admin can only be created from database
    $decodedData["user_type"] = "employee";
    $jsonData = json_encode($decodedData);

    //to validatte in the keys
    $keys = [
      'username' => ['empty', 'maxlength', 'format'],
      'password' => ['empty', 'maxlength', 'minLength', 'passwordFormat'],
      'email' => ['empty', 'email'],
      'name' => ['empty'],
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

      unset($checkIfUsernameExists["password"]);

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
    try {
      $authToken = self::getBrearerToken();
      $userObj = new User(new DBConnect());
      $authenticationObj = new JWTTokenHandlerAndAuthentication($userObj);

      $tokenAuthStatus = JWTTokenHandlerAndAuthentication::verifyToken($authToken);
      if ($tokenAuthStatus) {

        $jsonData = file_get_contents('php://input');
        //to validatte in the keys
        $decodedData = json_decode($jsonData, true);
        $keys = [
          'username' => ['empty', 'maxlength', 'format'],
          'password' => ['empty', 'maxlength', 'minLength'],
          'email' => ['empty', 'email'],
          'name' => ['empty'],
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
        $id = $_GET["id"];
        if (!$id) {
          throw new Exception("Id not provided !!");
        }
        $result = $userObj->get($id, NULL);
        if ($result["status"] == "false") {
          unset($result);
          return throw new Exception("User not found to update!!");
        }
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
    } catch (Exception $e) {
      return [
        "status" => "false",
        "statusCode" => 401,
        "message" => $e->getMessage()
      ];
    }
  }
  public static function deleteUser()
  {
    try {

      $authToken = self::getBrearerToken();
      $userObj = new User(new DBConnect());
      $authenticationObj = new JWTTokenHandlerAndAuthentication($userObj);

      $tokenAuthStatus = JWTTokenHandlerAndAuthentication::verifyToken($authToken);

      if ($tokenAuthStatus) {
        $id = $_GET["id"];
        if (!$id) {
          throw new Exception("Id not provided !!");
        }
        $result = $userObj->get($id, NULL);
        if ($result["status"] == "false") {
          unset($result);
          return throw new Exception("User not found to delete!!");
        }

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

    } catch (Exception $e) {
      return [
        "status" => "false",
        "message" => $e->getMessage()
      ];
    }
  }
  public static function getBrearerToken(): string
  {
    try {
      $authToken = $_SERVER["HTTP_AUTHORIZATION"] ?? false;

      if ($authToken === false) {
        throw new Exception("Authorization header not present!!");
      }
      $authToken = explode(" ", $authToken);

      if (count($authToken) !== 2 || $authToken[0] !== "Bearer") {
        throw new Exception("Invalid bearer token format.");
      }
      return $authToken[1];

    } catch (Exception $e) {

      error_log($e->getMessage());
      return "";
    }
  }

  /**
   * @return array with status
   * take stoken from authorization header and returns array with status true or false with null user_type if token is invalid
   */
  public static function getUserTypeFromToken()
  {
    try {

      $authToken = self::getBrearerToken();
      if ($authToken == "") {
        throw new Exception("Token not available");
      }
      $result = JWTTokenHandlerAndAuthentication::getSpecificValueFromToken($authToken, "user_type");

      return $result;
    } catch (Exception $e) {

      return [
        "status" => "false",
        "user_type" => "",
        "message" => $e->getMessage()
      ];
    }
  }
}

?>