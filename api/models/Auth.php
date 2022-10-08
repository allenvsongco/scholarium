<?php
class Auth extends API {

     public function __construct($request, $origin) {
          parent::__construct($request);
          $this->user = $this->request['user'];

// echo "$origin==".$_SERVER['SERVER_NAME']."\n\n";
//           if ($origin!=$_SERVER['SERVER_NAME'])
//                throw new Exception('Unauthorized access');

          if( !isset( $_SERVER['PHP_AUTH_USER'] ) ) {
               header('WWW-Authenticate: Basic realm="Scholarium"');
               header('HTTP/1.0 401 Unauthorized');
               echo 'Authentication FAILED';

               exit;

          } else {

               list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ) = explode(':' , base64_decode( substr( $_SERVER['HTTP_AUTHORIZATION'], 6) ) );
               $this->coid   = $_SERVER['PHP_AUTH_USER'];
               $this->apikey = $_SERVER['PHP_AUTH_PW'];

               $arrCoid = array(
                    'tujyBpbgtum3xcctFvXZgr4ZnaRsddVRpvkwJuq8B3KEwfd4BZQtrRaj5r4vdtDm' => 'TMTG',
                    'b3vg5vz5t6QJqRccTTysUtaYzF9bmUUrZXDNP54hxyF3Nr6azNdmAHXRrYjSQXA5' => 'Partner'
               );

               if ( array_key_exists($this->apikey, $arrCoid) && $arrCoid[$this->apikey] == $this->coid ) {
 
               } else
                    throw new Exception('Unauthorized access');

          }

     }

     protected function info() {
          $arr['data'] = [array(
               'scho_full' => 'Scholarium',
          )];

          return $arr;
     }

     protected function login() {
          $this->pass = $this->request['pass'];

          include_once 'config/DB.php';
          include_once 'models/Connect.php';

          $DB   = new DB();
          $db   = $DB->connect('scholarium');
          $post = new Connect($db);
          $rs   = $post->login($this->user, $this->pass);

          return $this->send($rs);
     }

     protected function me() {
          // /profile

          // params: ?dsdid=DIST_ID

          if ($this->method == 'GET') {
               $arg = $join = $wer = $and = $yr = $wk = $order = $view = $refno = '';
               $ret = '*';

               if (isset($this->verb)) {
                    $age = "(SELECT DATEDIFF(CONCAT(SUBSTRING(dssetd,1,4),'-',SUBSTRING(dssetd,5,2),'-',SUBSTRING(dssetd,-2)), '" . $this->pmstart . "') FROM diamondl_distributor.distributors WHERE dsdid=d.dsdid) age";
                    $ret  = "dsdid,CONCAT(dslnam,', ',dsfnam,' ',SUBSTRING(dsmnam,1,1)) name,dssid,(SELECT CONCAT(dslnam,', ',dsfnam,' ',SUBSTRING(dsmnam,1,1)) FROM distributors WHERE dsdid=d.dssid) sponsor,$age ";
                    $join = preg_match("/bomstp|bohstp|ormstp/i", $this->verb) ? 'LEFT JOIN ' . $this->verb . ' stp ON ' : '';
                    $find = "d.dsdid = '" . $this->user . "'";

                    if (!empty($this->args)) {
                         $yr = (!empty($this->args[0])) ? $this->args[0] : '';
                         $wk = (!empty($this->args[1])) ? $this->args[1] : '';

                         $act   = $yr;
                         $refno = $wk;
                    }

                    switch ($this->verb) {
                         case '':
                              break;

                         case 'profile':
                              $ret  .= ',d.*';
                              break;
                              
                        default:
                              return 'Invalid argument: ' . $this->verb;
                              exit;
                    }
               }

               $wer = "WHERE $find $and";

               include_once 'config/DB.php';
               include_once 'models/Connect.php';

               $DB   = new DB();
               $db   = $DB->connect('scholarium');
               $post = new Connect($db);
               $rs   = $post->distributors($this->user, $ret, $wer, $join, $order);

               return $this->send($rs);

          } else {
               http_response_code(405);
          }
     }

     protected function distributors() {
          // /find/[id|name]/[args]
          // /list
          // /list/[id]
          // /list/[id]/ormstp/[yr]/[mo]
          // /list/[id]/bomstp
          // /list/[id]/bohstp/[yr]/[mo]

          if($this->method == 'GET') {
               $id  = $arg = $join = $wer = $order = $and = $yr = $wk = '';
               $ret = '*';

               if(!empty($this->args)) {
                    $id = $this->args[0];

                    if(array_key_exists(1,$this->args) && preg_match("/find|list/i",$this->verb)) {
                         $yr   = (!empty($this->args[2]))?$this->args[2]:'';
                         $wk   = (!empty($this->args[3]))?$this->args[3]:'';
                         $join = preg_match("/bomstp|bohstp|ormstp/i",$this->args[1])?'LEFT JOIN '.$this->args[1].' stp ON ':'';

                         switch($this->args[1]) {
                              case 'bomstp':
                                   $join .= 'bmdid=dsdid';
                                   $order = '';
                                   break;

                              case 'bohstp':
                                   $join .= 'bhdid=dsdid';
                                   $order = 'ORDER BY bhpyr DESC,bhpmo DESC '.($yr!=''?'':'LIMIT 1');
                                   $and   = ($yr!=''?"AND bhpyr=$yr":'').($wk!=''?" AND bhpmo=$wk":'');
                                   break;

                              case 'ormstp':
                                   $join .= 'omdid=dsdid';
                                   $order = 'ORDER BY ompyr DESC,ompmo DESC '.($yr!=''?'':'LIMIT 1');
                                   $and   = ( $yr!='' ? "AND ompyr=$yr" : '' ) . ( $wk!='' ? " AND ompmo=$wk" : '' );
                                   break;

                              case '':
                                   break;

                              default:
                                   return 'Invalid argument: '.$this->args[1];
                                   exit;
                         }
                    }
               }

               $arg = implode(',',array_slice($this->args,1));

               switch($this->verb) {
                    case 'find':
                         $ret = "dsdid,CONCAT(dslnam,', ',dsfnam,' ',SUBSTRING(dsmnam,1,1)) name,dstin,dsmph ".($arg!=''?',stp.*':'');
                         $wer = "WHERE (dsdid='$id' OR LOWER(dsfnam) LIKE '%".strtolower($id)."%' OR LOWER(dslnam) LIKE '%".strtolower($id)."%' OR LOWER(dsmnam) LIKE '%".strtolower($id)."%') AND dsdid LIKE '".$this->user."%' $and";

                         if( empty($this->args) ) {
                              return 'Nothing to find';
                              exit;
                         }
                         break;

                    case 'list':
                         $ret = $id!='' ? $ret : 'dsdid';
                         $wer = "WHERE dsdid LIKE '".$this->user."%' ".($id!=''?" AND dsdid='$id'":'')." $and";
                         break;

                    default:
                         $id=$id!=''?$id:null;
                         $ret=$ret!=''?$ret:'*';
                         $wer="WHERE dsdid='$id'";
                         break;
               }

               include_once 'config/DB.php';
               include_once 'models/Connect.php';

               $DB = new DB();
               $db   = $DB->connect('scholarium');
               $post = new Connect($db);
               $rs = $post->distributors($id,$ret,$wer,$join,$order);

               return $this->send($rs);

          } else {
               http_response_code(405);
          }
     }

     protected function send($rs,$ret='') {
          $arr['data'] = array();

          if( is_array($rs) ) {
               array_push($arr['data'], $rs);
               return $arr;

          } elseif($rs->rowCount() > 0) {
               if($ret != '') $arr['lastid'] = $ret;

               while ($rw = $rs->fetch(PDO::FETCH_ASSOC)) {
                    foreach($rw as $k => $v) $item[$k] = $v;
                    array_push($arr['data'], $item);
               }

               return $arr;

          } else {
               return array('data'=>null);
          }
     }
}
?>
