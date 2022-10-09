<?php
/**
 *  @OA\Info(title="API", version="1.0")
 *   @OA\SecurityScheme(
 *        type="https",
 *        description="use /login to get JWT token",
 *        name="Authorization",
 *        in="header",
 *        scheme="bearer",
 *        bearerformat="JWT",
 *        securityScheme="bearerAuth",
 *   )
 */

require __DIR__ . '/../vendor/autoload.php';
use \Firebase\JWT\JWT;

define('SCLR_ROOT', $_SERVER['SERVER_NAME']);

class Connect {
     protected $conn;
     protected $asin;
     protected $key;

     public function __construct($db) {
          $this->conn = $db;
          $this->asin = 'AvTFQjVqsZ3f55oF';
          $this->key = hash('sha256', $this->asin);
     }

     public function login($un, $pw) {
          $password = sha1($un . $this->asin . $pw);

          $ret = "id,username,email";
          $qry = "SELECT $ret FROM user WHERE username='$un' AND password='$password' AND status=1";
          $ds = $this->conn->prepare($qry);
          $ds->execute();

          if( $ds->rowCount() > 0 ) {
               $dat = $ds->fetch();
               $iat = time();
               $exp = time() + (60 * 60);

               foreach ($dat as $k => $v) {
                    $$k = $v;
               }

               $payload = [
                    'iss' => SCLR_ROOT . '/api',  // issuer
                    'aud' => SCLR_ROOT,           // issuer
                    'iat' => $iat,                // time JWT was issued
                    'exp' => $exp,                // time JWT was expires, 60 mins
                    'data' => [
                         'id' => $id,
                         'username' => $username,
                         'email' => $email
                    ]
               ];

               $jwt = JWT::encode($payload,$this->key, 'HS256');
               return [
                    'token' => $jwt,
                    'expires' => $exp,
               ];
               return $payload;

          } else {
               return array('fail');
          };

     }

     public function users($id, $ret, $wer = '', $join = '', $order = '') {
          $qry = "SELECT $ret
               FROM user u
               $join
               $wer
               $order";

          $rs = $this->conn->prepare($qry);
          $rs->execute();
          return $rs;
     }

}
?>
