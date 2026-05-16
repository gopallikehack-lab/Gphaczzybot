<?php
// Database handler for SQLite3
class Database {
    private static $instance = null;
    private $db;
    
    private function __construct() {
        $this->db = new SQLite3(DB_PATH);
        $this->initTables();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    private function initTables() {
        // Users table
        $this->db->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER UNIQUE,
            username TEXT,
            first_name TEXT,
            last_name TEXT,
            last_active TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Messages table
        $this->db->exec("CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            message TEXT,
            is_from_admin INTEGER DEFAULT 0,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }
    
    public function getConnection() {
        return $this->db;
    }
}

function getDB() {
    return Database::getInstance()->getConnection();
}
?>
