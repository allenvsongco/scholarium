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

                    if (array_key_exists($this->apikey, $arrCoid) && $arrCoid[$this->apikey] == $this->coid) {
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
               http_response_code(405);
          }
     }

     protected function me()
     {
          // /profile
          // /education
          // /employment
          // /scholarship
          // /sparta_profile

          // params: ?user=USERNAME

          if ($this->method == 'GET') {
               $tbl = $join = $order = '';
               $ret = '*';

               if (isset($this->verb)) {
                    $ret  = "u.id,username,email,is_global,is_admin,is_partner ";

                    switch ($this->verb) {
                         case '':
                              break;

                         case 'profile':
                         case 'education':
                         case 'employment':
                         case 'scholarship':
                         case 'sparta_profile':
                              $join .= 'LEFT JOIN ' . $this->verb . ' stp ON stp.id=u.id';
                              $ret  .= ',stp.*';
                              break;
                              
                        default:
                              return 'Invalid argument: ' . $this->verb;
                              exit;
                    }
               }

               $wer = 'WHERE u.id = ';

               include_once 'config/DB.php';
               include_once 'models/Connect.php';

               $tbl  = 'user u';
               $DB   = new DB();
               $db   = $DB->connect('scholarium');
               $post = new Connect($db);
               $rs   = $post->auth($ret, $tbl, $wer, $join, $order);

               return $this->send($rs);

          } else {
               http_response_code(405);
          }
     }

     public function info()
     {
          $arr['data'] = [array(
               'scho_full' => 'Scholarium',
          )];

          return $arr;
     }
}
?>
