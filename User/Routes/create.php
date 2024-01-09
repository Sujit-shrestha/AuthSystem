<?php 
namespace Routes;

// require_once "../RequestHandlers/requestHandlers.php";
// require_once "../Middleware/response.php";

use RequestHandlers\RequestHandlers;
use Middleware\Response;

class Create{
  public static function create(){
     $response = RequestHandlers::createUser();
    Response::respondWithJson($response, $response["statusCode"]);
  }
}
 

?>