<?php
require_once 'config.php';

class Database {
    private $connection;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function escape($data) {
        return $this->connection->real_escape_string($data);
    }
    
    public function query($sql) {
        return $this->connection->query($sql);
    }
}

$db = new Database();
$conn = $db->getConnection();
?>