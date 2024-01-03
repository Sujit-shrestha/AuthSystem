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
  public function authenticate($username, $password)
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
         
          
          return true;
        } else {
          throw new \Exception("Unable to verify the password for given username.");
        }
      } else {
        return false;
      }

    } catch (\Exception $e) {
      echo "Unable to authenticate : " . $e->getMessage();
      $result = false;
    }
  }

  abstract public static function createToken(array $payload, int $exp);
  abstract public static function verifyToken(string $token);


}

class JWTTokenHandlerAndAuthentication extends Authentication
{

  static $token;
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
      static::$token = [
        "iat" => time(),
        "exp" => time() + $exp,
        "data" => $payload
      ];

      static::$token = JWT::encode(static::$token, static::$secret, static::$alg);

      return static::$token;

    } catch (\Exception $e) {
      echo '' . $e->getMessage() . '';
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
      echo "Token Expired";
      return false;

    } catch (\Firebase\JWT\SignatureInvalidException $e) {
      echo "Invalid token provided";
      return false;

    } catch (\Exception $e) {
      echo '' . $e->getMessage();
      return false;

    }

  }

  public static function getSpecificValueFromToken($authToken , $key ){
    try {
      
     
      $payload = JWT::decode($authToken, new key(static::$secret, static::$alg));
    
      return $payload->data->user_type;

  } catch (\Firebase\JWT\ExpiredException $e) {
    echo "Token Expired";
    return false;

  } catch (\Firebase\JWT\SignatureInvalidException $e) {
    echo "Invalid token provided";
    return false;

  } catch (\Exception $e) {
    echo '' . $e->getMessage();
    return false;

  }

  }

}

?>