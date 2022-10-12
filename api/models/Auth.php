<?php
class Auth extends API {

     public function __construct($request, $origin) {
          parent::__construct($request);

          $auth_type = strstr($_SERVER['HTTP_AUTHORIZATION'], ' ', 1);

          if ($auth_type == 'Basic') {
               if (!isset($_SERVER['PHP_AUTH_USER'])) {
                    header('WWW-Authenticate: Basic realm="Scholarium"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo 'Authentication FAILED';

                    exit;

               } else {

                    list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
                    $this->coid   = $_SERVER['PHP_AUTH_USER'];
                    $this->apikey = $_SERVER['PHP_AUTH_PW'];

                    $arrCoid = array(
                         'tujyBpbgtum3xcctFvXZgr4ZnaRsddVRpvkwJuq8B3KEwfd4BZQtrRaj5r4vdtDm' => 'TMTG',
                         'b3vg5vz5t6QJqRccTTysUtaYzF9bmUUrZXDNP54hxyF3Nr6azNdmAHXRrYjSQXA5' => 'Partner'
                    );

                    if (array_key_exists($_SERVER['PHP_AUTH_PW'], $arrCoid) && $arrCoid[$this->apikey] == $_SERVER['PHP_AUTH_USER']) {
                    } else
                         throw new Exception('unauthorized access');
               }
          }

     }

     protected function login() {
          if ($this->method == 'POST') {
               include_once 'config/DB.php';
               include_once 'models/Connect.php';

               $DB   = new DB();
               $db   = $DB->connect('scholarium');
               $post = new Connect($db);
               $rs   = $post->login($this->request['user'], $this->request['pass']);

               return $this->send($rs);

          } else {
               return 'invalid method';
          }
     }

     protected function me() {
          // /profile
          // /education
          // /employment
          // /scholarship
          // /sparta_profile

          include_once 'config/DB.php';
          include_once 'models/Connect.php';

          $DB   = new DB();
          $db   = $DB->connect('scholarium');
          $post = new Connect($db);
          $jwt  = $post->auth();

          if ($jwt) {
               // print_r($jwt);
               switch ($this->method) {
                    case 'GET':
                         $ret  = '*';
                         $join = '';

                         if (isset($this->verb)) {
                              $ret = "u.id";

                              switch ($this->verb) {
                                   case '':
                                        $ret  .= ',username,first_name,middle_name,last_name,is_admin';
                                        $join .= 'LEFT JOIN profile p ON p.id=u.id';
                                        break;

                                   case 'profile':
                                   case 'education':
                                   case 'employment':
                                   case 'scholarship':
                                   case 'sparta_profile':
                                        $ret  .= ',p.*';
                                        $join .= 'LEFT JOIN ' . $this->verb . ' p ON p.id=u.id';
                                        break;
                                        
                              default:
                                        return 'Invalid argument: ' . $this->verb;
                                        exit;
                              }
                         }

                         $tbl  = 'user u';
                         $wer  = 'WHERE u.id = ' . $jwt['id'];

                         $qry = "SELECT $ret
                         FROM $tbl
                         $join
                         $wer";

                         $rs   = $post->crud($qry);
                         return $this->send($rs);
                         // end me GET

                    case 'POST':
                         if (isset($this->verb)) {
                              switch ($this->verb) {
                                   case 'update':
                                   case 'password':
                                        break;

                                   default:
                                        return 'Invalid argument: ' . $this->verb;
                                        exit;
                              }
                         }

                         switch ($this->verb) {
                              case 'update':
                                   break;

                              case 'password':
                                   $rs = $post->pass($jwt['username'], $this->request);
                                   break;
                         }

                         return $this->send($rs);
                         // end me POST

                    default:
                         return 'invalid method';
                         exit;
               }

          } else {
               return 'invalid token';
               exit;
          }
          // end me
     }

     protected function users() {
          include_once 'config/DB.php';
          include_once 'models/Connect.php';

          $DB   = new DB();
          $db   = $DB->connect('scholarium');
          $post = new Connect($db);
          $jwt  = $post->auth();

          if ($jwt) {
               switch ($this->method) {
                    case 'GET':
                         $ret  = 'u.*,p.first_name,p.middle_name,p.last_name';
                         $join = '';

                         if (isset($this->verb)) {
                              switch ($this->verb) {
                                   case '':
                                        $join .= 'LEFT JOIN profile p ON p.id=u.id';
                                        break;

                                   default:
                                        return 'Invalid argument: ' . $this->verb;
                                        exit;
                              }
                         }

                         $tbl  = 'user u';
                         $order = 'ORDER BY u.id';

                         $qry = "SELECT $ret
                         FROM $tbl
                         $join
                         $order";

                         $rs = $post->crud($qry);
                         return $this->send($rs);

                         // end users GET

                    case 'POST':

                         if (isset($this->verb)) {
                              switch ($this->verb) {
                                   case 'update':
                                        break;

                                   case 'delete':
                                        break;

                                   default:
                                        return 'Invalid argument: ' . $this->verb;
                                        exit;
                              }
                         }

                         // switch ($this->verb) {
                         //      case 'create':
                         //      case 'update':
                         //      case 'delete':
                         //           $rs = $post->crud($qry);
                         //           break;
                         // }

                         // return $this->send($rs);

                         // end users POST

                    default:
                         return 'Invalid method: ' . $this->method;
                         exit;
               }

          } else {
               return 'invalid token';
               exit;
          }

          // end users
     }

     protected function basic() {
          $rs = [];

          switch ($this->method) {
               case 'GET';
                    if (isset($this->verb)) {
                         switch ($this->verb) {
                              case 'info':
                                   $rs = array(
                                        'scho_full' => 'Scholarium',
                                        );
                                   break;

                              default:
                                   return 'Invalid argument: ' . $this->verb;
                                   exit;
                         }
                    }
                    break;
                    // end basic GET

               case 'POST':
                    switch ($this->verb) {
                         case 'create':
                              include_once 'config/DB.php';
                              include_once 'models/Connect.php';

                              $DB   = new DB();
                              $db   = $DB->connect('scholarium');
                              $post = new Connect($db);

                              $usercheck = $post->crud("SELECT username FROM user WHERE username='". $this->request['username']."'");
                              $usercheck = $usercheck->fetch();

                              $mailcheck = $post->crud("SELECT email FROM user WHERE email='" . $this->request['email'] . "'");
                              $mailcheck = $mailcheck->fetch();

                              if (!empty($usercheck)) {
                                   return ['error' => 'username exists'];
                                   exit;

                              } elseif (!empty($mailcheck)) {
                                   return ['error' => 'email exists'];
                                   exit;

                              } else {
                                   foreach ($this->request as $k=>$v) $$k = $v;

                                   $hash = $post->getHash($username, $email);

                                   $post->crud("INSERT IGNORE INTO user (id,username,email,date_joined,hash) VALUES('','$username','$email',NOW(),'$hash')");
                                   
                                   $rs = $post->crud("SELECT id FROM user ORDER BY id DESC LIMIT 1")->fetch();
                                   $last_id = $rs['id'];

                                   $post->crud("INSERT IGNORE INTO profile (id,first_name,middle_name,last_name,last_modified) VALUES($last_id,'$first_name','$middle_name','$last_name',NOW())");

                                   return $this->send(['success' => 'user account created']);
                              }
                              break;

                         default:
                              return 'Invalid argument: ' . $this->verb;
                              exit;
                    }
                    break;
                    // end basic POST

               default:
                    return 'Invalid method: ' . $this->method;
                    exit;
          }

          // end basic
     }
}
?>
