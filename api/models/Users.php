<?php
class Users extends API {
     protected $jwtdata;

     public function __construct($request, $origin) {
          parent::__construct($request);

          $this->user = $this->request['user'];

          $headers = apache_request_headers();

          if (isset($headers['Authorization'])) {
               $token = trim(str_replace('Bearer ', '', $headers['Authorization']));

               try {
                    $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
                    $payload = json_decode(json_encode((array) $decoded), true);
                    $jwtdata = $payload['data'];

               } catch (\Exception $e) {
                    return ['Invalid token'];
               }
          }

     }

     protected function info() {
          $arr['data'] = [array(
               'scho_full' => 'Scholarium',
          )];

          return $arr;
     }

     protected function me() {
          // /profile
          // /education
          // /employment
          // /scholarship
          // /sparta_profile

          // params: ?user=USERNAME

          if ($this->method == 'GET') {
               $join = $wer = $order = '';
               $ret = '*';

               if (isset($this->verb)) {
                    $ret  = "u.id,username,email,is_global,is_admin,is_partner ";
                    $join = preg_match("/profile|education|employment/i", $this->verb) ? 'LEFT JOIN ' . $this->verb . ' stp ON ' : '';
                    $find = "u.hash = '" . $this->token . "'";

                    switch ($this->verb) {
                         case '':
                              break;

                         case 'profile':
                         case 'education':
                         case 'employment':
                         case 'scholarship':
                         case 'sparta_profile':
                              $join .= 'stp.id=u.id';
                              $ret  .= ',stp.*';
                              break;
                              
                        default:
                              return 'Invalid argument: ' . $this->verb;
                              exit;
                    }
               }

               $wer = "WHERE $find";

               include_once 'config/DB.php';
               include_once 'models/Connect.php';

               $DB   = new DB();
               $db   = $DB->connect('scholarium');
               $post = new Connect($db);
               $rs   = $post->users($this->user, $ret, $wer, $join, $order);

               return $this->send($rs);

          } else {
               http_response_code(405);
          }
     }
}
?>
