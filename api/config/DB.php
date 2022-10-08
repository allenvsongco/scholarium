<?php
class DB{
     private $host = 'localhost';
     private $dbas = 'u778836748_';
     private $un   = 'scholar';
     private $pw   = 'tAXnO7EGAEIo5D5n';
     private $conn;

     public function connect($db) {
          $this->conn = null;

          try {
               $this->conn = new PDO(
                    'mysql:host=' . $this->host . ';dbname=' . $this->dbas . $db,
                    $this->dbas . $this->un,
                    $this->pw,
                    [
                         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                         PDO::ATTR_EMULATE_PREPARES => false,
                    ]
               );
          } catch(PDOException $e) { die($e->getMessage()); }

          return $this->conn;
     }
}
?>
