<?php

require_once "./Middleware/authentication.php";
require_once "./Configuration/database-connection.php";
require_once "./Model/user-mdoel.php";
require_once "./Configuration/session.php";


use Middleware\JWTTokenHandlerAndAuthentication;
use Configg\DBConnect;
use Model\User;
use Configg\Session;


switch ($_SERVER["REQUEST_METHOD"]) {
  case "GET":

    break;

  case "POST":
    //
    if (isset($_POST["username"]) && isset($_POST["password"])) {

      //authenticate user 
      $authenticationObj = new JWTTokenHandlerAndAuthentication(new User(new DBConnect()));

      $status = $authenticationObj->authenticate($_POST["username"], $_POST["password"]);

      if ($status) {
        echo "User Authenticated by usernmae password. ";

        //defining payload
        $payload = array(
          "username"=> $_POST["username"],
          "password"=> $_POST["password"],
        );
        //creating JWT token 

       $authToken = JWTTokenHandlerAndAuthentication::createToken($payload );
        
       
        //creating Session

        Session::create();
       setcookie("authToken" , $authToken, time() + 3600,"/");


      }
    }
    break;

  case "PUT":

    break;

  case "DELETE":
}

?>