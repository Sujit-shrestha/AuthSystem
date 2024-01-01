<?php 

use Middleware\JWTTokenHandlerAndAuthentication;
use Configg\DBConnect;
use Model\User;
use Configg\Session;

 //authenticate user 
 function login(){
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
  $authToken = JWTTokenHandlerAndAuthentication::createToken($payload);
   
  //sets user_type in session
 $_SESSION["login"] = true;
  setcookie("authToken" , $authToken, time() + 3600,"/");

 }
 }
 

?>