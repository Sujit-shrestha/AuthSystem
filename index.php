<?php 
 require_once __DIR__ ."/User/Configuration/config.php";
use Routes\Route;


 Route::user("/user" , function () {
  include "./User/index.php";
 });


?>