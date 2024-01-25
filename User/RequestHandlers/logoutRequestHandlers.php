<?php 

namespace RequestHandlers;

use Exception;
use Middleware\Authentication;
use Middleware\JWTTokenHandlerAndAuthentication;
use Model\TokenBlackList;
use Validate\Validator;
use Configg\DBConnect;
use Middleware\Authorization;

class LogoutRequestHandlers
{
  public static function logout(){
    try{
    
      
      die("logoutreqhandler");

  
  

  $result = JWTTokenHandlerAndAuthentication::expireToken();
  if(!$result["status"]){
    throw new Exception($result["message"]);
  }
  print_r($result);
  die();

    }catch(Exception $e){
      return [
        "status" => "false",
        "statusCode" => 401 ,
        "message" => $e->getMessage()
      ];
    }
  }
}