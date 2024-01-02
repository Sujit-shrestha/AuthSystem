<?php


/**
 *
 */

 interface AuthorizationInterface {
  public static function checkPermission(string $permission_type) : bool;

 }

 class Authorization implements AuthorizationInterface {

  public static function checkPermission(string $permission_type) : bool{
    session_start();
    $user_type = $_SESSION["user_type"]??NULL;
  

    $permissions = [
      "" =>[],
      "admin" => ["edit" , "delete" ] , 
      "normal" =>["create"]
    ];


    if(in_array($permission_type ,$permissions[$user_type] )){
/////code remaining
    return true;
    }else{
      return false;
    }

  }
 }
  
echo Authorization::checkPermission("admin");






?>