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


use Routes\Create;
use Routes\Delete;
use Routes\Get;
use Routes\Update;


class Admin
{
  public static function run()
  {
    $uri = $_SERVER['REQUEST_URI'];
    switch ($_SERVER["REQUEST_METHOD"]) {
      case "GET":
        /**
         * gets data and sends  respoonse
         */
        //check if the route is /admin/get
        if (preg_match('/^\/admin\/get(?:\/[^?]+)?(?:\?.+)?$/', $uri)) {
          Get::get();
        }

        break;

      case "POST":
        if (preg_match('/^\/admin\/create(?:\/[^?]+)?(?:\?.+)?$/', $uri)) {
          Create::create();
        }

        break;

      case "PUT":
        if (preg_match('/^\/admin\/update(?:\/[^?]+)?(?:\?.+)?$/', $uri)) {

          Update::update();
        }

        break;

      case "DELETE":
        if (preg_match('/^\/admin\/delete(?:\/[^?]+)?(?:\?.+)?$/', $uri)) {
          Delete::delete();

        }
        break;
      

      default:
      echo "Invalid Request";
        break;
    }
  }
}

?>