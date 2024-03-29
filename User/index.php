<?php
namespace Index;

session_start();


require_once __DIR__ . "/Configuration/config.php";


use AccessControl\Admin;
use RequestHandlers\RequestHandlers;
use Middleware\Response;
use Routes\Route;

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$pathOptions = [
                "/department",
                "/location",
                "/category",
                "/logout",
                "/login"
              ];

//dyniamically creating callback names
if (in_array($path, $pathOptions)) {
  $trimmedPath = trim($path, '/');
  $className = ucfirst($trimmedPath);

  Route::$trimmedPath($path, "Routes\\" . $className . '\\' . $className . '::run');
  //expected format  'Routes\Location\\Location::run'
  exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && ($path === '/user')) {

  //creating case for no admin token 
  if ($path === '/user') {
    //directly callig create to bypass auth for direct signup
    Route::user($path, "Routes\\Create::create");
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
    Admin::run();

    break;

  case "employee":
    $response = [
      "status" => false,
      "message" => "Employee is unauthorised to use the system for now"
    ];
    Response::respondWithJson($response, 401);

    break;
}
