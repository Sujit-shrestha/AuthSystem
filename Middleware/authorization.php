<?php

use Firebase\JWT\JWT;
use Firebase\JWT\key;

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

  public static function verifyToken()
  {


  }
}



?>