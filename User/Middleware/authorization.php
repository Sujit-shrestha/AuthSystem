<?php

namespace Middleware;

use \Firebase\JWT\JWT;
use \Firebase\JWT\key;
use Middleware\JWTTokenHandlerAndAuthentication;
/**
 *
 */

 interface AuthorizationInterface {
  public static function checkPermission(string $permission_type) : bool;

 public static function verifyToken():array;
 }

 class Authorization implements AuthorizationInterface {

  public static function verifyToken():array 
  {
    try {

      $result = self::getBrearerToken();
      if(!$result["status"]){
       throw new \Exception($result["message"]);
      } 
      
      static $token = $result["data"]["token"];
      static $payload = [];

      $payload = JWT::decode($token, new key(JWTTokenHandlerAndAuthentication::$secret, JWTTokenHandlerAndAuthentication::$alg));
      
      return [
        "status" => true,
        "message" => "User authorised using authToken.",
        "data" => [
          "id" =>$payload->data ->id,
          "username" => $payload->data ->username,
          "user_type"=> $payload->data ->user_type
        ]
      ];

    } catch (\Firebase\JWT\ExpiredException $e) {
      // echo "Token Expired";
      error_log($e->getMessage());
      return [
        "status"=> false,
        "message"=> $e->getMessage(),
        "data" => []
      ];

    } catch (\Firebase\JWT\SignatureInvalidException $e) {
      // echo "Invalid token provided";
      error_log($e->getMessage());
      return  [
        "status"=> false,
        "message"=> $e->getMessage(),
        "data" => []
      ];
    } catch (\Exception $e) {
      error_log($e->getMessage());
      return [
        "status"=> false,
        "message"=> "Invalid Token : ".$e->getMessage(),
        "data"=> []
      ];
    }

  }

  public static function getBrearerToken(): array
  {
    try {
      $authToken = $_SERVER["HTTP_AUTHORIZATION"] ?? false;

      if ($authToken === false) {
        throw new \Exception("Authorization header not present!!");
      }
      $authToken = explode(" ", $authToken);

      if (count($authToken) !== 2 || $authToken[0] !== "Bearer") {
        throw new \Exception("Invalid bearer token format.");
      }
      return [
        "status" => true,
        "message" => "Token extracted successully .",
        "data"=> ["token" => $authToken[1]]
      ];

    } catch (\Exception $e) {

      return [
        "status" => false,
        "message" => $e->getMessage(),
        "data"=> []
      ];
    }
  }

  public static function checkPermission(string $permission_type) : bool{
    session_start();
    $user_type = $_SESSION["user_type"]??NULL;
  

    $permissions = [
      "" =>[],
      "admin" => ["PUT" , "DELETE" ] , 
      "employee" =>["POST"]
    ];


    if(in_array($permission_type ,$permissions[$user_type] )){
/////code remaining
    return true;
    }else{
      return false;
    }
  }

  
 }

