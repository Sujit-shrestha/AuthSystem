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
require_once "./RequestHandlers/requestHandlers.php";

use RequestHandlers\RequestHandlers;
use Middleware\Response;
use Routes\Login;





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

    $user_type = RequestHandlers::getUserTypeFromToken();

    if ($user_type == "admin") {
      $response = RequestHandlers::getByIdOrUsername();
      Response::respondWithJson($response, $response["statusCode"]);
    } else if ($user_type == "normal") {
      Response::respondWithJson(array("status" => "false", "message" => "Unauthorised"), 401);
    }

    break;

  case "POST":

    $response = RequestHandlers::createUser();
    Response::respondWithJson($response, $response["statusCode"]);

    break;

  case "PUT":
    $user_type = RequestHandlers::getUserTypeFromToken();

    if ($user_type == "admin") {
      Response::respondWithJson(RequestHandlers::updateUser(), 200);
    } else if ($user_type == "normal") {
      Response::respondWithJson(array("status" => "false", "message" => "Unauthorised"), 401);
    }

    break;

  case "DELETE":
    $user_type = RequestHandlers::getUserTypeFromToken();

    if ($user_type == "admin") {

      Response::respondWithJson(RequestHandlers::deleteUser(), 200);
    } else if ($user_type == "normal") {
      Response::respondWithJson(array("status" => "false", "message" => "Unauthorised"), 401);
    }

    break;
}



?>