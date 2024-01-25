<?php

namespace Routes;

class Route
{

  public static function user($endpoint, $callbackFunction)
  {
    $endpoint = explode("/", trim($endpoint, "/"));
     
    if ($endpoint[0] == "login") {
      $callbackFunction();
    }
    if ($endpoint[0] == "user") {
      $callbackFunction();
    }
  }

  public static function category($endpoint, $callbackFunction){
    $endpoint = explode("/", trim($endpoint, "/"));
    if ($endpoint[0] == "category") {
      $callbackFunction();
    }
  }

  public static function location($endpoint , $callbackFunction){
    
    $endpoint = explode("/" , trim($endpoint , "/"));
    $callbackFunction();
  }

  public static function department($endpoint , $callbackFunction)
  {
    $callbackFunction();
  }
  public static function logout($endpoint , $callbackFunction){
    $callbackFunction();
  }
  public static function login($endpoint , $callbackFunction){
    $callbackFunction();
  }
  public static function post($endpoint, $callbackFunction)
  {
    // $endpoint = explode("/", trim($endpoint, "/"));
    // if ($endpoint[0] == "create") {
    //   $callbackFunction();
    // }
    $callbackFunction();
  }

  public static function get($endpoint, $callbackFunction)
  {
    // $endpoint = explode("/", trim($endpoint, "/"));
    // if (substr($endpoint[0], 0, 3) === "get") {
    //   $callbackFunction();
    // }

    $callbackFunction();
  }
  public static function put($endpoint, $callbackFunction)
  {
    $endpoint = explode("/", trim($endpoint, "/"));

    if (substr($endpoint[0], 0, 6) == "update") {
      $callbackFunction();
    }
  }
  public static function delete($endpoint, $callbackFunction)
  {
    $endpoint = explode("/", trim($endpoint, "/"));

    if (substr($endpoint[0], 0, 6) == "delete") {
      $callbackFunction();
    }
  }
}
