<?php
/**
 * Configuration File
 * H&S Inventory Management System
 * 
 * This file loads configuration from .env file
 * For legacy support, it also checks for config.local.php
 */

// Check if .env file exists
$envFile = dirname(__DIR__) . '/.env';
$legacyConfigFile = __DIR__ . '/config.local.php';

if (file_exists($envFile)) {
    // Modern approach: Load from .env file
    require_once __DIR__ . '/env.php';
} elseif (file_exists($legacyConfigFile)) {
    // Legacy approach: Load from config.local.php
    require_once $legacyConfigFile;
    
    // Define constants that might not be in legacy config
    if (!defined('ENVIRONMENT')) {
        define('ENVIRONMENT', 'production');
    }
    
    if (!defined('APP_NAME')) {
        define('APP_NAME', 'Health and Safety');
    }
    
    if (!defined('APP_ROOT')) {
        define('APP_ROOT', dirname(__DIR__));
    }
    
    if (!defined('DB_CHARSET')) {
        define('DB_CHARSET', 'utf8mb4');
    }
    
    // Session Configuration
    if (!defined('SESSION_NAME')) {
        define('SESSION_NAME', 'h_s_inventory_session');
    }
    if (!defined('SESSION_LIFETIME')) {
        define('SESSION_LIFETIME', 604800); // 1 week
    }
    if (!defined('SESSION_PATH')) {
        define('SESSION_PATH', '/');
    }
    if (!defined('SESSION_DOMAIN')) {
        define('SESSION_DOMAIN', '');
    }
    if (!defined('SESSION_SECURE')) {
        define('SESSION_SECURE', (ENVIRONMENT === 'production'));
    }
    if (!defined('SESSION_HTTPONLY')) {
        define('SESSION_HTTPONLY', true);
    }
    
    // Timezone
    if (!defined('APP_TIMEZONE')) {
        date_default_timezone_set('America/New_York');
    }
    
    // Error Handling
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', APP_ROOT . '/logs/error.log');
    
    // File Upload Settings
    if (!defined('MAX_FILE_SIZE')) {
        define('MAX_FILE_SIZE', 5242880); // 5MB
    }
    if (!defined('ALLOWED_FILE_TYPES')) {
        define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);
    }
    if (!defined('UPLOAD_PATH')) {
        define('UPLOAD_PATH', APP_ROOT . '/public/uploads/');
    }
    
    // Feature Flags
    if (!defined('ENABLE_PASSWORD_RESET')) {
        define('ENABLE_PASSWORD_RESET', true);
    }
    if (!defined('ENABLE_AUDIT_LOG')) {
        define('ENABLE_AUDIT_LOG', true);
    }
    
    // Installation marker
    if (!defined('INSTALLATION_COMPLETED')) {
        define('INSTALLATION_COMPLETED', true);
    }
    
    // Dynamic URL detection
    if (!defined('APP_URL')) {
        require_once __DIR__ . '/dynamic_url.php';
    }
} else {
    // No configuration found - show error
    die('Configuration Error: Neither .env file nor config.local.php found. Please copy .env.example to .env and configure it.');
}
