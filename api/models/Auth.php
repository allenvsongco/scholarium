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
// echo $this->verb;
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
                                        return 'invalid argument: ' . $this->verb;
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
                                        return 'invalid argument: ' . $this->verb;
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

     protected function admin() {
          // /admin/users
          // /admin/users/[list|profile|education|employment|scholarship|sparta_profile]

          include_once 'config/DB.php';
          include_once 'models/Connect.php';

          $DB   = new DB();
          $db   = $DB->connect('scholarium');
          $post = new Connect($db);
          $jwt  = $post->auth();

          if ($jwt && $jwt['is_admin']) {
               switch ($this->method) {
                    case 'GET':
                         $ret = $join = $wer = '';

                         if (isset($this->verb)) {
                              switch ($this->verb) {
                                   case 'users':

                                        if (!empty($this->args)) {
                                             $arg = (!empty($this->args[0])) ? $this->args[0] : '';

                                             switch ($arg) {
                                                  case 'list':
                                                       $ret  .= 'u.*,p.first_name,p.middle_name,p.last_name';
                                                       $join .= 'LEFT JOIN profile p ON p.id=u.id';

                                                       if (count($this->request) > 1) {
                                                            $wer = 'WHERE ';

                                                            foreach ($this->request as $k => $v) {
                                                                 if ($k != 'request') {
                                                                      $wer .= "$k='$v' AND ";
                                                                 }
                                                            }
                                                            $wer = substr($wer, 0, -4);
                                                       }
                                                       break;

                                                  case 'profile':
                                                  case 'education':
                                                  case 'employment':
                                                  case 'scholarship':
                                                  case 'sparta_profile':
                                                       if (isset($this->request['id'])) {
                                                            $ret  .= 'u.id,p.*';
                                                            $join .= "LEFT JOIN $arg p ON p.id=u.id";
                                                            $wer   = 'WHERE u.id = ' . $this->request['id'];

                                                       } else {
                                                            return 'argument parameter: id';
                                                            exit;
                                                       }
                                                       break;

                                                  default:
                                                       return 'argument missing';
                                                       exit;
                                             }

                                        } else {
                                             return 'argument missing';
                                             exit;
                                        }
                                        break;

                                   default:
                                        return 'invalid argument: ' . $this->verb;
                                        exit;
                              }
                         }

                         $tbl  = 'user u';
                         $order = 'ORDER BY u.id';

                         $qry = "SELECT $ret
                         FROM $tbl
                         $join
                         $wer
                         $order";
// echo $qry;
                         $rs = $post->crud($qry);
                         return $this->send($rs);

                         // end admin GET

                    case 'POST':

                         if (isset($this->verb)) {
                              switch ($this->verb) {
                                   case 'update':
                                        break;

                                   case 'delete':
                                        break;

                                   default:
                                        return 'invalid argument: ' . $this->verb;
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

                         // end admin POST

                    default:
                         return 'invalid method: ' . $this->method;
                         exit;
               }

          } else {
               return 'invalid token';
               exit;
          }

          // end admin
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
                                   return 'invalid argument: ' . $this->verb;
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
                              return 'invalid argument: ' . $this->verb;
                              exit;
                    }
                    break;
                    // end basic POST

               default:
                    return 'invalid method: ' . $this->method;
                    exit;
          }

          // end basic
     }
}
?>
