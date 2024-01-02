<?php 
namespace Middleware;

class Response {

  public static function respondWithJson($data , $status){
    header('Content-type : application/json');
    http_response_code($status);
    echo json_encode($data , JSON_PRETTY_PRINT);
  }
}


?>