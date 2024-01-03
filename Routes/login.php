<?php
namespace Routes;
use Middleware\Response;
use Middleware\JWTTokenHandlerAndAuthentication;
use Configg\DBConnect;
use Model\User;
use Configg\Session;

//authenticate user 
class Login
{
  public static function login()
  {
    $authenticationObj = new JWTTokenHandlerAndAuthentication(new User(new DBConnect()));

    $status = $authenticationObj->authenticate($_POST["username"], $_POST["password"]);

    if ($status) {
 
      //defining payload
      $payload = array(
        "username" => $_POST["username"],
        "password" => $_POST["password"],
        "user_type" => $_SESSION["user_type"],
        "id"=>$_SESSION["id"] //taking id from session as id is injected when authenticated
      );
 
      //creating JWT token 
      $authToken = JWTTokenHandlerAndAuthentication::createToken($payload);

      //sets login true 
      $_SESSION["login"] = true;
      setcookie("authToken", $authToken, time() + 3600, "/");

      Response::respondWithJson(array("status"=> "success","authToken"=> $authToken , "message" => "User authenticated successfully.") , 200);
      
    }else{
      setcookie("authToken", "", time() + 3600, "/");
      Response::respondWithJson(array("status"=> "false","message"=> "Unable to login.") , 403);
    }
  }
}



?>