<?php
// Database configuration

// Check if we're in the installer
$inInstaller = strpos($_SERVER['REQUEST_URI'] ?? '', 'installer.php') !== false;

// Check if installation is complete
$installLock = dirname(__DIR__) . '/install.lock';
$installerFile = dirname(__DIR__) . '/installer.php';

// Skip installer redirect since we have install.lock

// Load database config from config.php if it exists (created by installer)
$configFile = __DIR__ . '/config.php';
if (file_exists($configFile)) {
    // Config file should define DB_HOST, DB_NAME, DB_USER, DB_PASS
    require_once $configFile;
}

// During installation, don't attempt any database connection
if ($inInstaller && !file_exists($installLock)) {
    // Define empty constants to prevent errors
    if (!defined('DB_HOST')) define('DB_HOST', '');
    if (!defined('DB_NAME')) define('DB_NAME', '');
    if (!defined('DB_USER')) define('DB_USER', '');
    if (!defined('DB_PASS')) define('DB_PASS', '');
    if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');
    
    // Create a dummy Database class that does nothing during installation
    class Database {
        private static $instance = null;
        
        private function __construct() {
            // Do nothing during installation
        }
        
        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        
        public function getConnection() {
            // Return null during installation
            return null;
        }
        
        public function isConnected() {
            return false;
        }
    }
    
    return; // Stop processing the rest of the file
}

// Only define charset if not already defined
if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}

// Create database connection class
class Database {
    private static $instance = null;
    private $connection = null;
    
    private function __construct() {
        // Only attempt connection if database constants are defined
        if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS')) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
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
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        if ($this->connection === null) {
            throw new Exception("Database connection not available. Please check your database configuration.");
        }
        return $this->connection;
    }
    
    public function isConnected() {
        return $this->connection !== null;
    }
}