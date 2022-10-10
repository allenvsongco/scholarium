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
               $exp = time() + (60 * 60); // 60 mins from issue

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
               return ['fail'];
          };

     }

     public function auth($ret, $tbl, $wer = '', $join = '', $order = '')
     {
          $headers = apache_request_headers();

          if(isset($headers['Authorization'])) {
               $token = trim(str_replace('Bearer ', '', $headers['Authorization']));

               try {
                    $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
                    $payload = json_decode(json_encode((array) $decoded), true);

                    $data = $payload['data'];
                    $src  = $data['id'];

                    $qry = "SELECT $ret
                    FROM $tbl
                    $join $wer $src $order";

                    $rs = $this->conn->prepare($qry);
                    $rs->execute();
                    return $rs;

               } catch (\Exception $e) {
                    return ['Invalid token'];
               }
          }

     }

}
?>
