<?php
namespace Middleware;

include_once "../Model/user-mdoel.php";
use Model\User;


use Firebase\JWT\JWT;
use Firebase\JWT\key;

/** 
 * checks if the user is present in the database
 */
abstract class Authentication
{
  private $userModel;

  public function __construct(User $userModel)
  {
    $this->userModel = $userModel;

  }
  public function authenticate($username, $password)
  {
    try {
      $result = $this->userModel->get(null, $username);
      if ($result["status"] == false) {
        throw new \Exception("Unable to find the user.");
      } else {
        if (password_verify($password, $result["password"])) {
          return true;
        } else {
          throw new \Exception("Unable to verify the password");
        }
      }

    } catch (\Exception $e) {
      echo "Unable to authenticate : " . $e->getMessage();
      $result = false;
    }
  }

  abstract public static function createToken();
  abstract public static function verifyToken();


}

class JWTTokenHandler extends Authentication
{

  static $token;
  static $secret = "INTUJI_SECRET KEY";
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
}

?>