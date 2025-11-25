<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $connection = null;
    
    private function __construct()
    {
        // Only attempt connection if database constants are defined
        if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS')) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . (defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4');
                $this->connection = new PDO($dsn, DB_USER, DB_PASS);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                // Log error but don't die immediately - let the app handle it
                error_log("Database connection failed: " . $e->getMessage());
                $this->connection = null;
            }
        }
    }
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection()
    {
        if ($this->connection === null) {
            throw new \Exception("Database connection not available. Please check your database configuration.");
        }
        return $this->connection;
    }
    
    public function isConnected()
    {
        return $this->connection !== null;
    }
}
