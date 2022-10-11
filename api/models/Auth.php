<?php
class Auth extends API {

     public function __construct($request, $origin)
     {
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
                         throw new Exception('Unauthorized access');
               }
          }

     }

     protected function login()
     {
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

     protected function me()
     {
          // /profile
          // /education
          // /employment
          // /scholarship
          // /sparta_profile

          // params: ?id=USER_ID

          if ($this->method == 'GET') {
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

               include_once 'config/DB.php';
               include_once 'models/Connect.php';

               $DB   = new DB();
               $db   = $DB->connect('scholarium');
               $post = new Connect($db);
               $jwt  = $post->auth();
// print_r($jwt);
               if ($jwt) {
                    $tbl  = 'user u';
                    $wer  = 'WHERE u.id = ' . $jwt['id'];

                    $qry = "SELECT $ret
                    FROM $tbl
                    $join
                    $wer";

                    $rs   = $post->crud($qry);
                    return $this->send($rs);

               } else {
                    return 'invalid token';
                    exit;
               }

          } elseif ($this->method == 'POST') {

               if (isset($this->verb)) {
                    switch ($this->verb) {
                         case 'create':
                              break;

                         case 'update':
                              break;

                         case 'delete':
                              break;

                         case 'password':
                              $comm = 'UPDATE';
                              $tbl  = 'user';
                              $add  = 'SET password=';
                              $wer  = 'WHERE username=';
                              break;

                         default:
                              return 'Invalid argument: ' . $this->verb;
                              exit;
                    }
               }

               include_once 'config/DB.php';
               include_once 'models/Connect.php';

               $DB   = new DB();
               $db   = $DB->connect('scholarium');
               $post = new Connect($db);
               $jwt  = $post->auth();

               if ($jwt) {
                    $qry = "$comm $tbl $add $wer";
// echo $qry;
                    switch ($this->verb) {
                         case 'create':
                              break;

                         case 'update':
                              break;

                         case 'delete':
                              break;

                         case 'password':
                              $rs = $post->pass($this->request['user'], $this->request['pass'], $comm, $tbl, $add, $wer);
                              break;
                    }

                    return $this->send($rs);

               } else {
                    return 'invalid token';
                    exit;
               }

          } else {
               return 'invalid method';
          }
     }

     protected function users()
     {
          // /profile
          // /education
          // /employment
          // /scholarship
          // /sparta_profile

          if ($this->method == 'GET') {
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

               include_once 'config/DB.php';
               include_once 'models/Connect.php';

               $DB   = new DB();
               $db   = $DB->connect('scholarium');
               $post = new Connect($db);
               $jwt  = $post->auth();

               if ($jwt) {
                    $tbl  = 'user u';
                    $order = 'ORDER BY u.id';

                    $qry = "SELECT $ret
                    FROM $tbl
                    $join
                    $order";

                    $rs = $post->crud($qry);
                    return $this->send($rs);

               } else {
                    return 'invalid token';
               }

          } else {
               return 'invalid method';
          }
     }

     protected function info()
     {
          $arr['data'] = [array(
               'scho_full' => 'Scholarium',
          )];

          return $arr;
     }
}
?>
