<?php

namespace AccessControl;

require_once "./Configuration/config.php";
require_once "./Middleware/authentication.php";
require_once "./Configuration/database-connection.php";
require_once "./Model/user-mdoel.php";
require_once "./Configuration/session.php";
require_once "./Routes/login.php";
require_once "./Routes/get.php";
require_once "./Routes/update.php";
require_once "./Routes/delete.php";
require_once "./Middleware/response.php";
require_once "./Middleware/response.php";
require_once "./RequestHandlers/requestHandlers.php";
require_once "./Routes/route.php";


use Routes\Create;
use Routes\Delete;
use Routes\Get;
use Routes\Update;
use Routes\Route;


class Admin
{
  public static function run()
  {
    $uri = $_SERVER['REQUEST_URI'];
    //sets the route after/admin
    $uri = explode('/', $uri)[2];
    
  

    switch ($_SERVER["REQUEST_METHOD"]) {
      case "GET":
     
        Route::get($uri , "Routes\\Get::get");

        break;

      case "POST":

       Route::post($uri , "Routes\\Create::create");

        break;

      case "PUT":
       
        Route::put($uri ,'Routes\\Update::update');

        break;

      case "DELETE":
      
        Route::delete($uri ,'Routes\\Delete::delete');
        break;
      

      default:
      echo "Route/request not found";
        break;
    }
  }
}

?>