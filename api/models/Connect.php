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
use \Firebase\JWT\Key;

define('SCLR_ROOT', $_SERVER['SERVER_NAME']);

class Connect {
     protected $conn;
     protected $key;

     public function __construct($db)
     {
          include_once 'config/asin.config';

          $this->conn = $db;
          $this->asin = $asin;
          $this->key  = $key;
     }

     public function login($un, $pw)
     {
          $password = sha1($un . $this->asin . $pw);

          $ret = "id,username,email";
          $qry = "SELECT $ret FROM user WHERE username='$un' AND password='$password' AND status=1";
          $rs = $this->conn->prepare($qry);
          $rs->execute();

          if( $rs->rowCount() > 0 ) {
               $dat = $rs->fetch();
               $iat = time();

               $delay = 60;  // expires x mins from issue
               $exp = time() + (60 * $delay);

               foreach ($dat as $k => $v) {
                    $$k = $v;
               }

               $payload = [
                    'iss' => SCLR_ROOT . '/api',  // issuer
                    'aud' => SCLR_ROOT,           // audience
                    'iat' => $iat,                // time JWT was issued
                    'exp' => $exp,                // time JWT was expires
                    'data' => [
                         'id' => $id,
                         'username' => $username,
                         'email' => $email
                    ]
               ];

               $jwt = JWT::encode($payload, $this->key, 'HS256');
               return [
                    'token' => $jwt,
                    'expires' => $exp,
               ];

          } else {
               return false;
          };

     }

     public function pass($un, $pw, $comm, $tbl, $add, $wer)
     {
          $pass = sha1($un . $this->asin . $pw);
          $qry = "$comm $tbl $add '$pass' $wer '$un'";

          $rs = $this->conn->prepare($qry);
          $rs->execute();
          return ['success'=>'password changed'];
     }

     public function crud($qry)
     {
// echo $qry;
          $rs = $this->conn->prepare($qry);
          $rs->execute();
          return $rs;
     }

     public function auth()
     {
          $headers = apache_request_headers();

          if (isset($headers['Authorization'])) {
               $token = trim(str_replace('Bearer ', '', $headers['Authorization']));

               try {
                    $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
                    $payload = json_decode(json_encode((array) $decoded), true);

                    return $payload['data'];

               } catch (\Exception $e) {
                    return false;
               }
          }
     }

}
?>
