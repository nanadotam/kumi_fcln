<?php
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        // Load database configuration
        $config = require_once __DIR__ . '/../db/config.php';
        
        // Create connection using the returned connection from config
        $this->connection = $config;
        
        if ($this->connection->connect_error) {
            throw new Exception("Connection failed: " . $this->connection->connect_error);
        }
        
        $this->connection->set_charset("utf8mb4");
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        return $result;
    }
    
    public function begin_transaction() {
        return $this->connection->begin_transaction();
    }
    
    public function commit() {
        return $this->connection->commit();
    }
    
    public function rollback() {
        return $this->connection->rollback();
    }
    
    public function lastInsertId() {
        return $this->connection->insert_id;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}
