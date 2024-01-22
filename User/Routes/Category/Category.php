<?php

namespace Routes\Category;

use Middleware\Response;

use RequestHandlers\CategoryRequestHandlers;

class Category
{
  public static function run()
  {

    switch ($_SERVER['REQUEST_METHOD']) {
      case 'GET':
        if (isset($_GET['parent'])) {
          self::getByParent();
        } else {
          self::getAll();
        }
        break;

      case 'POST':
        self::create();
        break;

      case 'PUT':
        if (isset($_GET["previousParent"])) {

          self::updateParent();
        } else if (isset($_GET["previousChild"])) {

          self::updateChild();
        } else {
          Response::respondWithJson([
            "status" => "false",
            "message" => "Parameters are not provided!!"
          ], 400);
        }
        break;

      case 'DELETE':
        if (isset($_GET['childCategory'])) {
          self::deleteChild();

        } else if (isset($_GET['parentCategory'])) {
          self::deleteParent();
        } else {
          Response::respondWithJson([
            "status" => "false",
            "message" => "Parameters are not provided!!"
          ], 400);
        }
        break;
    }
  }
  public static function create()
  { 
    $response = CategoryRequestHandlers::createCategory();
    Response::respondWithJson($response, $response["statusCode"]);
  }
  public static function getAll()
  {
    $response = CategoryRequestHandlers::getAll();
    Response::respondWithJson($response, $response["statusCode"]);
  }
  public static function getByParent()
  {
    $response = CategoryRequestHandlers::getByParent();
    Response::respondWithJson($response, $response["statusCode"]);
  }
  public static function updateParent()
  {
    $response = CategoryRequestHandlers::updateParent();
    Response::respondWithJson($response, $response["statusCode"]);
  }
  public static function updateChild()
  {
    $response = CategoryRequestHandlers::updateChild();
    Response::respondWithJson($response, $response["statusCode"]);
  }
  public static function deleteChild()
  {
    $response = CategoryRequestHandlers::deleteChild();
    Response::respondWithJson($response, $response["statusCode"]);
  }
  public static function deleteParent(){
    $response = CategoryRequestHandlers::deleteParent();
    Response::respondWithJson($response, $response["statusCode"]);
  }
}

