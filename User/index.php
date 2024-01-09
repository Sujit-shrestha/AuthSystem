<?php
//ra
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
require_once "./AccessControl/admin.php";
require_once "./Routes/create.php";

use AccessControl\Admin;
use RequestHandlers\RequestHandlers;
use Middleware\Response;
use Routes\Login;
use Routes\Create;





/**
 * Switch to handle requests
 * 
 * 
 */
//seperating login part//sets $_SESSION["user_type]

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
if ($_SERVER["REQUEST_METHOD"] == "POST" && (($path === '/login') || ($path === '/create'))) {
  if ($path === '/login') {
    $response = Login::login();
    Response::respondWithJson($response, $response["statusCode"]);
    exit();
  }
  if ($path === '/create') {

    ////create path for post ...creating user
    Create::create();
    exit();
  }
}

//getting user type form given auth token
$result = RequestHandlers::getUserTypeFromToken();
if (isset($result["user_type"]) && $result["user_type"] == "") {
  $response = $result;
  Response::respondWithJson($response, 401);
}

switch ($result["user_type"]) {
  case "admin":
    $uri = $_SERVER['REQUEST_URI'];

    // Regular expression to check if the URI starts with /admin
    $pattern = '/^\/admin/';

    if (preg_match('/^\/admin/', $uri)) {
      Admin::run();
    }
    break;

  case "employee":
    $response = [
      "status" => false,
      "message" => "Employee is unauthorised to use the system for now"
    ];

    Response::respondWithJson($response, 401);
    break;

    // Route::post("create", "createUser");
}
?>