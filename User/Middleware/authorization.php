<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\key;
use Middleware\JWTTokenHandlerAndAuthentication;
/**
 *
 */

 interface AuthorizationInterface {
  public static function checkPermission(string $permission_type) : bool;

 public static function verifyToken(string $token):array;
 }

 class Authorization implements AuthorizationInterface {

  public static function verifyToken(string $token):array 
  {
    try {
      static $token = $token;
      static $payload = [];
      self::$payload = JWT::decode($token, new key(JWTTokenHandlerAndAuthentication::$secret, JWTTokenHandlerAndAuthentication::$alg));

      
      return [
        "status" => true,
        "message" => "User authorised using authToken.",
        "data" => [
          "id" => $payload["id"],
          "username" => $payload["username"],
          "user_type"=> $payload["user_type"]
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






?>