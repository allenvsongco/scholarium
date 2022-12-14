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

date_default_timezone_set('Asia/Manila');

require __DIR__ . '/../vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

define('SCLR_ROOT', $_SERVER['SERVER_NAME']);

class Connect {
     protected $conn;
     protected $key;

     public function __construct($db) {
          require 'config/asin.config';

          $this->conn = $db;
          $this->asin = $asin;
          $this->key  = $key;
     }

     public function login($un, $pw) {
          $password = sha1($un . $this->asin . $pw);

          $ret = "id,username,email,is_global,is_admin,is_partner";
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
                         'email' => $email,
                         'is_global' => $is_global,
                         'is_admin' => $is_admin,
                         'is_partner' => $is_partner,
                    ]
               ];

               $qry = "UPDATE user SET last_login=NOW() WHERE username='$un'";
               $rs = $this->conn->prepare($qry);
               $rs->execute();

               $jwt = JWT::encode($payload, $this->key, 'HS256');
               return [
                    'token' => $jwt,
                    'expires' => $exp,
               ];

          } else {
               return ['error' => 'invalid username or password'];
          };

     }

     public function pass($user, $post) {
          $old = $post['oldpw'];
          $new = $post['newpw'];

          $pass = sha1($user . $this->asin . $old);
          $test = "SELECT COUNT(id) pass FROM user WHERE username='" . $user . "' AND password='$pass'";
          $tst  = $this->conn->prepare($test);
          $tst->execute();
          $test = $tst->fetch();

          if ($test['pass']) {
               $pass = sha1($user . $this->asin . $new);
               $qry = "UPDATE user SET password='$pass' WHERE username='$user'";

               $rs = $this->conn->prepare($qry);
               $rs->execute();
               return ['success'=>'password changed'];

          } else {
               return ['error'=>'invalid username or password'];
          }
     }

     public function reset_pass($user)
     {
          $qry = "SELECT id,username,hash FROM user WHERE username='" . $user . "'";
          $rs = $this->conn->prepare($qry);
          $rs->execute();
          $r = $rs->fetch();

          if ($rs->rowCount() > 0) {
               $pass = sha1($r['username'] . $this->asin . sha1( $r['hash']));
               $qry = "UPDATE user SET password='$pass' WHERE id='" . $r['id'] . "'";

               $rs = $this->conn->prepare($qry);
               $rs->execute();
               return [
                    'success' => 'password reset',
                    'new_pass' => sha1($r['hash'])
               ];

          } else {
               return ['error' => 'invalid username'];
          }
     }

     public function set_kiu($post) {
          $kdata = $idata = $udata = '';

          // $ints  = "/\bid\b|is_employed|first_timer|is_active|is_global|is_admin|is_partner|status/i";

          foreach ($post as $k => $v) {
               // $v = trim_escape($v);
               $$k = $v;

               $kdata .= " $k,";

               // if ($k == 'last_modified') {
               //      $v = date(TMDSET);
               // }

               // $vvv = preg_match($ints, $k) ? $v : "'$v'";
               $vvv = "'$v'";

               $idata .= "$vvv,";
               $udata .= $k . "=$vvv,";
          }

          $kdata = substr($kdata, 0, -1);
          $idata = substr($idata, 0, -1);
          $udata = substr($udata, 0, -1);

          return array($kdata, $idata, $udata);
     }

     public function getHash($h1, $h2) {
          return sha1($h1 . $this->asin . $h2);
     }

     public function crud($qry, $return=0) {
// echo $qry;
          $rs = $this->conn->prepare($qry);
          $rs->execute();

          if($return) {
               $this->conn->lastInsertId();
          } else {
               return $rs;
          }
     }

     public function auth() {
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
