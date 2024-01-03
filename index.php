<?php

namespace Index;

require_once "./Configuration/config.php";
require_once "./Middleware/authentication.php";
require_once "./Configuration/database-connection.php";
require_once "./Model/user-mdoel.php";
require_once "./Configuration/session.php";
require_once "./Routes/login.php";
require_once "./Middleware/response.php";
require_once "./Middleware/response.php";


use Exception;
use Middleware\JWTTokenHandlerAndAuthentication;
use Configg\DBConnect;
use Model\User;
use Configg\Session;
use Middleware\Response;
use Routes\Login;


function respondWithJson($data, $status)
{
  header('Content-type : application/json');
  http_response_code($status);
  echo json_encode($data);
}



/** 
 * takes auth  ,verifies , gives  response
 */
function getByIdOrUsername()
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

    $response = array(
      "success" => "true",
      "status" => "200",
      "message" => "Data extraceted.",
      "data" => $result
    );
    Response::respondWithJson($response, $response["status"]);
  } else {

    $response = array(
      "success" => "false",
      "status" => "401",
      "message" => "Unauthorised to get."
    );

    Response::respondWithJson($response, $response["status"]);
  }

  //disconnecting from database
  $userObj->DBconn->disconnectFromDatabase();
}
function createUser()
{
  //creating user so auth not required

  $userObj = new User(new DBConnect());
  $jsonData = file_get_contents('php://input');

  $result = $userObj->create($jsonData);
  if ($result) {
    $response = array(
      "success" => "true",
      "status" => "201",
      "message" => "User created successfully"
    );
    Response::respondWithJson($response, $response["status"]);
  } else {
    $response = array(
      "success" => "false",
      "status" => "409",
      "message" => "Unable to create user"
    );
    Response::respondWithJson($response, $response["status"]);
  }
}

function updateUser()
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

      $response = array(
        "success" => "true",
        "status" => "201",
        "message" => "User Updated successfully",
        "updatedData" => json_decode($jsonData)
      );
      Response::respondWithJson($response, $response["status"]);

    } else {
      print_r($updateStatus);
    }
  }
  //disconnecting from database
  $userObj->DBconn->disconnectFromDatabase();
}
function deleteUser()
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


    } else {
      $response = array(
        "success" => "false",
        "status" => "500",
        "message" => "$deleteStatus"
      );
      Response::respondWithJson($response, $response["status"]);
    }

  }

  //disconnecting from database
  $userObj->DBconn->disconnectFromDatabase();

}
function getBrearerToken()
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
function getUserTypeFromToken()
{
  try {
    $authToken = getBrearerToken();

    return JWTTokenHandlerAndAuthentication::getSpecificValueFromToken($authToken, "user_type");
  } catch (Exception $e) {
    return $e->getMessage();
  }
}
/**
 * Switch to handle requests
 * 
 * 
 */
//seperating login part//sets $_SESSION["user_type]
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
if ($_SERVER["REQUEST_METHOD"] == "POST" && $path === '/login') {
  if ($path === '/login') {
    Login::login();
    exit();
  }
}


switch ($_SERVER["REQUEST_METHOD"]) {
  case "GET":


    $user_type = getUserTypeFromToken();
   
    if ($user_type == "admin") {
      getByIdOrUsername();
    }else if($user_type == "normal"){
      Response::respondWithJson(array("status"=> "false" , "message"=> "Unauthorised") , 401);
    }


    break;

  case "POST":
    $user_type = getUserTypeFromToken();
   
    if ($user_type == "admin" || $user_type == "normal") {
    
    createUser();
    }else{
      Response::respondWithJson(array("status"=> "false" , "message"=> "Unauthorised") , 401);
    }

    break;

  case "PUT":
    $user_type = getUserTypeFromToken();
   
    
    if ($user_type == "admin") {
      updateUser();
    }else if($user_type == "normal"){
      Response::respondWithJson(array("status"=> "false" , "message"=> "Unauthorised") , 401);
    }




    break;

  case "DELETE":
    $user_type = getUserTypeFromToken();
   
    if ($user_type == "admin") {
        deleteUser();
    }else if($user_type == "normal"){
      Response::respondWithJson(array("status"=> "false" , "message"=> "Unauthorised") , 401);
    }
  


    break;
}



?>