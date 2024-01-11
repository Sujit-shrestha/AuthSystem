<?php 

namespace Routes\Category;

use Middleware\Response;

use RequestHandlers\CategoryRequestHandlers;

class Category {
  public static function create(){
    echo "therheirh";
    $response = CategoryRequestHandlers::createCategory();
    Response::respondWithJson($response, $response["statusCode"]);
  }
}

?>