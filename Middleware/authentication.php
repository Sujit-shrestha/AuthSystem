<?php
namespace Middleware;

require_once 'vendor/autoload.php';

include_once "../Model/user-mdoel.php";
include_once "../Configuration/session.php";


use Model\User;
use Configg\Session;

use \Firebase\JWT\JWT;
use \Firebase\JWT\key;

/** 
 * checks if the user is present in the database
 * developer can add multiple tokenization /verification
 *  techniques
 */
abstract class Authentication
{
  private $userModel;

  public function __construct(User $userModel)
  {
    $this->userModel = $userModel;

  }
  /**
   * @return_ true on verified and array on 
   * exception
   */
  public function authenticate($username, $password):bool
  {
    try {
     
      $result = $this->userModel->get(null, $username);

      if ($result) {
        if (password_verify($password, $result["password"])) {
          //user gets authenticated if code reaches here

          //adding user_type into session
          Session::create();
         
         
          $_SESSION["id"] = $result["id"];
          $_SESSION["user_type"] = $result["user_type"];
         session_write_close();
         
          
          return true;
        } else {
          throw new \Exception("Unable to verify for given password provided!!");
        }
      } else {
        throw new \Exception("Unable to get from database on given username!!");
       
      }

    } catch (\Exception $e) {
      error_log($e->getMessage());
      return false;
    }
  }

  abstract public static function createToken(array $payload, int $exp);
  abstract public static function verifyToken(string $token);


}

class JWTTokenHandlerAndAuthentication extends Authentication
{

  static $token=[];
  static $secret = "INTUJI_SECRET KEY";
  // static $secretForNormalUser = "PINKUJI_SECRET KEY";
  static $alg = 'HS256';

  /**
   * can create token with static call
   * @param array payload  , int exp
   * @ return token||false
   */
  public static function createToken(array $payload, int $exp = 3600)
  {
    try {
      self::$token = [
        "iat" => time(),
        "exp" => time() + $exp,
        "data" => $payload
      ];

      self::$token = JWT::encode(self::$token, self::$secret, self::$alg);

      return self::$token;

    } catch (\Exception $e) {
      error_log($e->getMessage());
      return false;
    }


  }
  /**
   * verifies provided token
   * @param string token
   * @return bool
   */
  public static function verifyToken(string $token): bool
  {
    try {
      static::$token = $token;
      static::$token = JWT::decode($token, new key(static::$secret, static::$alg));

      return true;

    } catch (\Firebase\JWT\ExpiredException $e) {
      // echo "Token Expired";
      error_log($e->getMessage());
      return false;

    } catch (\Firebase\JWT\SignatureInvalidException $e) {
      // echo "Invalid token provided";
      error_log($e->getMessage());
      return false;
    } 

  }

  public static function getSpecificValueFromToken($authToken , $key ){
    try {
      
     
      $payload = JWT::decode($authToken, new key(self::$secret, self::$alg));
    $user_type =  $payload->data->user_type;
 
      return [
        "status"=>true ,
        "user_type" => $user_type,
        "message" => "Users user_type has been found!!"
      ];

  } catch (\Firebase\JWT\ExpiredException $e) {
    // echo "Token Expired";
    return [
      "status"=>false,
      "user_type" => "",
      "message" => "Token Expired"
    ];

  } catch (\Firebase\JWT\SignatureInvalidException $e) {
    // echo "Invalid token provided";
    return [
      "status"=>false,
      "user_type" => "",
      "message" => "Invalid token provided"
    ];

  } catch (\Exception $e) {
    // echo '' . $e->getMessage();
    return [
      "status"=>false,
      "user_type" => "",
      "message" => $e->getMessage()
    ];

  }

  }

}

?>