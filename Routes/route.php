<?php

namespace Routes;

require_once "./Routes/create.php";
require_once "./Routes/login.php";
require_once "./Routes/get.php";
require_once "./Routes/update.php";
require_once "./Routes/delete.php";

use Routes\Create;
use Routes\Delete;
use Routes\Get;
use Routes\Update;

class Route
{
  public static function post($endpoint, $callbackFunction)
  {
    // $data = file_get_contents("php://input");
  // var_dump($data);
  //   die;
    $endpoint = explode("/", trim($endpoint, "/"));

    if ($endpoint[0] == "create") {
      $callbackFunction();
    }
  }

  public static function get($endpoint, $callbackFunction)
  {
    $endpoint = explode("/", trim($endpoint, "/"));
if(substr($endpoint[0], 0, 3) === "get"){
      $callbackFunction();
    }
  }
  public static function put($endpoint, $callbackFunction)
  {
    $endpoint = explode("/", trim($endpoint, "/"));
   
    if ( substr($endpoint[0], 0, 6) == "update") {
      $callbackFunction();
    }
  }
  public static function delete($endpoint, $callbackFunction)
  {
    $endpoint = explode("/", trim($endpoint, "/"));

    if (substr($endpoint[0], 0, 6) =="delete") {
      $callbackFunction();
    }
  }
}
?>