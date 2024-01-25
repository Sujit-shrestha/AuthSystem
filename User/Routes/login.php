<?php
namespace Routes;

use Middleware\JWTTokenHandlerAndAuthentication;
use Configg\DBConnect;
use Model\User;
use Middleware\Response;

class Login
{
  public static function login()
  {
    $userObj = new User(new DBConnect());
    $authenticationObj = new JWTTokenHandlerAndAuthentication($userObj);

    $status = $authenticationObj->authenticate($_POST["username"], $_POST["password"]);
    if(!$status){
      $response = [
        "status" => "false",
        "message" => "Unable to authenticate the user.",
        "statusCode" => 401
      ];
      Response::respondWithJson($response, $response["statusCode"]);
    }
    $userData = $userObj->get(NULL , $_POST["username"]);
      //defining payload
      $payload = array(
        "user_type" =>  $userData["user_type"],
        "id" => $userData["id"],
      );
      //creating JWT token 
      $authToken = JWTTokenHandlerAndAuthentication::createToken($payload);

      //storing access_token in session
      session_start();
      $_SESSION["authToken"] = $authToken;
      session_write_close();

      // print_r($_SESSION);
      $respose_payload = [
        "access_token" => $authToken ,
        "user_id"=> $userData["id"],
        "user_type" =>$userData["user_type"]
      ];

      $response = [
        "status" => true,
        "message" => "User authenticated successfully.",
        "payload" => $respose_payload
      ];
      Response::respondWithJson($response);
  }
}

