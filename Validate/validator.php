<?php
namespace Validate;
class Validator
{

  /**
   * @param $data Associative array of eg : [username => "Rikesh" , password => passkey ]
   * @return NULL|array
   * eg:
   * $data = [
  'username' => 'Aadssa',
  'password' => '2342',
  'email' => 'example@example.com',
];
$keys = [
  'username' => ['empty', 'maxLength', 'format'],
  'password' => ['empty', 'maxLength', 'minLength']
];
   */
  public static function validate($data, $keys)
  {
    $validateData = [];

    foreach ($keys as $key => $value) {
      //check empty 
      if (in_array('empty', $value)) {
        if (isset($data[$key]) && !empty($data[$key])) {

        } else {
          $validateData[$key] = [
            $key . " is required!"
          ];
        }
      }

      //check max length
      if (in_array("maxLength", $value)) {
        $maxAllowedLength = [
          "password" => "40",
          "username" => 25,
          "email" =>254, 
          "address" => 254,
        ];

        $inputLength = strlen($data[$key]);

        if ($inputLength > $maxAllowedLength[$key])
          $validateData[$key] = [
            $key . " exceeded max length!"
          ];

      }
      if(in_array("minLength" ,$value)){
        //rules for keys
        $minAllowedLength = [
          "password" => "8" ,
          "username"=> "5",
          "email" => "5" ,
          "address" => "2" ,
          "name" => "2"
        ];

        $inputLength = strlen($data[$key]);

        if ($inputLength < $minAllowedLength[$key])
          $validateData[$key] = [
            $key . " must be of minimum length $minAllowedLength[$key]!"
          ];
      }

      //check format of the data --Alphnumeric format
      if (in_array("format", $value)) {
        function isValidUsername($username)
        {
          // Check if the string starts with an alphabet and contains only alphabets and numbers
          return preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', $username);
        }


        isValidUsername($data[$key]) ?[] : $validateData[$key] = [$key . " format is not valid!"];
      }
      
      if(in_array("email", $value)) {
        function isValidEmail($email){

        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        }
      isValidEmail($data[$key]) ? [] : $validateData[$key] = [$key . " is not valid."];

    }
  }
    if (count($validateData) > 0) {
      return [
        "validate" => false,
        "message" => $validateData
      ];
    }
    return [
      "validate" => true,
      "message" => $validateData
    ];
  }
}
?>