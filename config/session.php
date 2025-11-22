<?php
/**
 * Session Management Helper
 * Centralizes session configuration and initialization
 */

class SessionManager {
    private static $initialized = false;
    
    /**
     * Initialize session with proper configuration
     */
    public static function init() {
        if (self::$initialized) {
            return;
        }
        
        // Check if session is already active
        if (session_status() === PHP_SESSION_ACTIVE) {
            self::$initialized = true;
            return;
        }
        
        // Load configuration if not already loaded
        if (!defined('SESSION_LIFETIME')) {
            require_once dirname(__FILE__) . '/config.php';
        }
        
        // Configure session parameters before starting
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
        ini_set('session.cookie_lifetime', SESSION_LIFETIME);
        ini_set('session.name', 'HANDSIMS_SESSION');
        
        // Set cookie parameters
        $cookieParams = [
            'lifetime' => SESSION_LIFETIME,
            'path' => SESSION_PATH,
            'domain' => '',
            'secure' => SESSION_SECURE,
            'httponly' => SESSION_HTTPONLY,
            'samesite' => 'Lax'
        ];
        
        // PHP 7.3+ supports array parameter
        if (version_compare(PHP_VERSION, '7.3.0', '>=')) {
            session_set_cookie_params($cookieParams);
        } else {
            session_set_cookie_params(
                $cookieParams['lifetime'],
                $cookieParams['path'],
                $cookieParams['domain'],
                $cookieParams['secure'],
                $cookieParams['httponly']
            );
        }
        
        // Start the session
        session_start();
        
        // Regenerate session ID periodically for security
        // Use a long interval so it doesn't interfere with long-lived sessions
        $regenInterval = min(SESSION_LIFETIME, 86400); // at most daily, within lifetime
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > $regenInterval) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
        
        self::$initialized = true;
    }
    
    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated() {
        self::init();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Get session value
     */
    public static function get($key, $default = null) {
        self::init();
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Set session value
     */
    public static function set($key, $value) {
        self::init();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Check if session key exists
     */
    public static function has($key) {
        self::init();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove session value
     */
    public static function remove($key) {
        self::init();
        unset($_SESSION[$key]);
    }
    
    /**
     * Destroy session
     */
    public static function destroy() {
        self::init();
        
        // Unset all session values
        $_SESSION = [];
        
        // Delete the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the session
        session_destroy();
        self::$initialized = false;
    }
    
    /**
     * Flash message system
     */
    public static function setFlash($key, $message) {
        self::init();
        $_SESSION['flash'][$key] = $message;
    }
    
    public static function getFlash($key) {
        self::init();
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }
    
    public static function hasFlash($key) {
        self::init();
        return isset($_SESSION['flash'][$key]);
    }
}
?>