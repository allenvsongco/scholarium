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

                    require_once 'config/asin.config';

                    list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
                    $this->coid   = $_SERVER['PHP_AUTH_USER'];
                    $this->apikey = $_SERVER['PHP_AUTH_PW'];

                    if (array_key_exists($_SERVER['PHP_AUTH_PW'], $arrCoid) && $arrCoid[$this->apikey] == $_SERVER['PHP_AUTH_USER']) {
                    } else
                         throw new Exception('unauthorized access');
               }
          } // end basic auth
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
     // end login endpoint
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

          // test jwt token
          if ($jwt) {
               switch ($this->method) {
                    case 'GET':
                         $ret  = '*';
                         $join = '';

                         if (isset($this->verb)) {
                              $ret = "u.id";

                              switch ($this->verb) {
                                   case '':
                                        $ret  .= ',username,first_name,middle_name,last_name,is_global,is_admin,is_partner';
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
                                   case 'user':
                                   case 'profile':
                                   case 'education':
                                   case 'employment':
                                   case 'scholarship':
                                   case 'sparta_profile':
                                        if (!empty($this->args)) {
                                             $arg = (!empty($this->args[0])) ? $this->args[0] : '';

                                             switch ($arg) {
                                                  case 'update':
                                                       if (count($this->request) > 0) {
                                                            $this->request['id'] = $jwt['id'];
                                                            $tbl = $this->verb;

                                                            list($kdata, $idata, $udata) = $post->set_kiu($this->request);
                                                            $qry = "INSERT INTO $tbl ($kdata) VALUES($idata) ON DUPLICATE KEY UPDATE $udata";

                                                            $rs = $post->crud($qry, 1);
                                                            return $this->send(['success' => 'user account updated']);
                                                       }
                                                       break;

                                                  default:
                                                       return 'invalid argument: ' . $this->verb;
                                                       exit;
                                             }
                                        }
                                        break;

                                   case 'password':
                                        $rs = $post->pass($jwt['username'], $this->request);
                                        break;

                                   default:
                                        return 'invalid argument: ' . $this->verb;
                                        exit;
                              }

                         } else {
                              return 'verb missing';
                              exit;
                         }

                         return $this->send($rs);
                    // end me POST

                    default:
                         return 'invalid method';
                         exit;
               } // end switch method

          } else {
               return 'invalid token';
               exit;
          } // end test jwt token

     } // end me endpoint

     protected function admin() {
          // /admin/users
          // /admin/users/[list|profile|education|employment|scholarship|sparta_profile]

          include_once 'config/DB.php';
          include_once 'models/Connect.php';

          $DB   = new DB();
          $db   = $DB->connect('scholarium');
          $post = new Connect($db);
          $jwt  = $post->auth();

          // test jwt token
          if ($jwt) {
               // test user is_admin
               if ($jwt['is_admin']) {
                    switch ($this->method) {
                         case 'GET':
                              $ret = $join = $wer = '';

                              if (isset($this->verb)) {
                                   switch ($this->verb) {
                                        case 'users':

                                             if (!empty($this->args)) {
                                                  $arg = (!empty($this->args[0])) ? $this->args[0] : '';
                                                  $arg1= (!empty($this->args[1])) ? $this->args[1] : '';

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

                                                       case 'user':
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
                              break;
                         // end admin GET

                         case 'POST':
                              if (isset($this->verb)) {
                                   switch ($this->verb) {
                                        case 'users':
                                             if (!empty($this->args)) {
                                                  $arg = (!empty($this->args[0])) ? $this->args[0] : '';
                                                  $arg1= (!empty($this->args[1])) ? $this->args[1] : '';

                                                  switch ($arg) {
                                                       case 'user':
                                                       case 'profile':
                                                       case 'education':
                                                       case 'employment':
                                                       case 'scholarship':
                                                       case 'sparta_profile':
                                                           if (!empty($this->args[1])) {
                                                                 switch ($arg1) {
                                                                      case 'update':
                                                                           $tbl = $arg;

                                                                           if (count($this->request) > 0) {
                                                                                list($kdata, $idata, $udata) = $post->set_kiu($this->request);
                                                                                $qry = "INSERT INTO $tbl ($kdata) VALUES($idata) ON DUPLICATE KEY UPDATE $udata";

                                                                                $rs = $post->crud($qry, 1);
                                                                                return $this->send(['success' => 'user account updated']);
                                                                           }
                                                                           break;

                                                                      default:
                                                                           return 'invalid argument';
                                                                           exit;
                                                                 }
                                                            }
                                                            break;

                                                       case 'delete':
                                                            if (isset($this->request['id'])) {
                                                                 $qry = "SELECT id FROM user WHERE id=" . $this->request['id'];
                                                                 $rs = $post->crud($qry);

                                                                 if ($rs->rowCount() > 0) {
                                                                      $qry = "DELETE FROM user WHERE id=" . $this->request['id'];
                                                                      $rs = $post->crud($qry);
                                                                      return $this->send(['success' => 'user account deleted']);

                                                                 } else {
                                                                      return $this->send(['error' => 'invalid id']);
                                                                 }

                                                            } else {
                                                                 return 'invalid argument';
                                                            }
                                                            break;

                                                       case 'reset_pass':
                                                            if (isset($this->request['username'])) {
                                                                 $rs = $post->reset_pass($this->request['username']);
                                                                 return $this->send($rs);

                                                            } else {
                                                                 return 'invalid argument';
                                                            }
                                                            break;

                                                       default:
                                                            return 'invalid argument: ' . $arg;
                                                            break;
                                                  }
                                             }
                                             break;
                                             // end switch users

                                        default:
                                             return 'invalid argument: ' . $this->verb;
                                             exit;
                                   } // end switch verb
                              }
                              break;
                         // end admin POST

                         default:
                              return 'invalid method: ' . $this->method;
                              exit;

                    } // end switch method

               } else {
                    return 'unauthorized user';
                    exit;
               }
               // end test user is_admin

          } else {
               return 'invalid token';
               exit;
          } // end basic method

     } // end admin endpoint

     protected function basic() {
          $rs = [];

          switch ($this->method) {
               case 'GET';
                    if (isset($this->verb)) {

                         switch ($this->verb) {
                              case 'info':
                                   $rs = array(
                                        'full' => 'Scholarium',
                                        'domain' => 'scholarium.io',
                                        );

                                   return $this->send($rs);
                                   break;

                              default:
                                   return 'invalid argument: ' . $this->verb;
                                   exit;
                         }

                    } else {
                         return 'argument missing';
                         exit;
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

                              $usercheck = $post->crud("SELECT username FROM user WHERE username='" . $this->request['username'] . "'");
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

                                   return $this->send([
                                        'success' => 'user account created',
                                        'hash' => $hash
                                   ]);
                              }
                              break;

                         case 'verify':
                              if (isset($this->request['hash'])) {
                                   include_once 'config/DB.php';
                                   include_once 'models/Connect.php';

                                   $DB   = new DB();
                                   $db   = $DB->connect('scholarium');
                                   $post = new Connect($db);
                                   $pass = sha1($this->request['hash']);

                                   $qry = "UPDATE user SET status=1,password='" . $pass . "' WHERE hash='" . $this->request['hash'] . "'";
                                   $post->crud($qry);

                                   $user = $post->crud("SELECT username FROM user WHERE hash='" . $this->request['hash'] . "'");
                                   $user = $user->fetch();

                                   return $this->send([
                                        'success' => 'user account verified',
                                        'username' => $user['username'],
                                        'password' => $pass
                                   ]);

                              } else {
                                   return ['error' => 'missing argument'];
                                   exit;
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

          } // end basic method

     } // end basic endpoint
}
?>
