<?php
class Connect {
     protected $conn;
     protected $asin;

     public function __construct($db) {
          $this->conn = $db;
          $this->asin = 'AvTFQjVqsZ3f55oF';
     }

     public function login($un, $pw) {
          $password = sha1($un . $this->asin . $pw);

          $ret = "id,username,email,is_global,is_admin,is_partner";
          $qry = "SELECT $ret FROM user WHERE username='$un' AND password='$password' AND status=1";
          $ds = $this->conn->prepare($qry);
          $ds->execute();

          if( $ds->rowCount() > 0 ) {
               return $ds;

          } else {
               return array('fail');
          };

     }

     public function users($id, $ret, $wer = '', $join = '', $order = '') {
          $qry = "SELECT $ret
               FROM user u
               $join
               $wer
               $order";

          $rs = $this->conn->prepare($qry);
          $rs->execute();
          return $rs;
     }

}
?>
