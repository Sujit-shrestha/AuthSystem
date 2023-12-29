<?php

use Firebase\JWT\JWT;
use Firebase\JWT\key;

/**
 * created abstract class to let developer add multiple authoization techniques
 */
abstract class Authorization
{

  abstract public static function createToken();
  abstract public static function verifyToken();
}

class JWTTokenHandler extends Authorization
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
  public static function verifyToken(string $token):bool
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