<?php
/**
 * Environment Configuration Loader
 * Loads configuration from .env file using phpdotenv
 */

use Dotenv\Dotenv;

// Load Composer autoloader if not already loaded
if (!class_exists('Dotenv\Dotenv')) {
    require_once dirname(__DIR__) . '/vendor/autoload.php';
}

// Load .env file
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Validate required environment variables
$dotenv->required([
    'APP_ENV',
    'APP_NAME',
    'APP_URL',
    'DB_HOST',
    'DB_NAME',
    'DB_USER',
    'DB_PASS',
    'SECURE_AUTH_KEY',
    'SECURE_AUTH_SALT'
])->notEmpty();

// Helper function to get environment variables with default values
if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
        
        // Convert string booleans to actual booleans
        if (is_string($value)) {
            switch (strtolower($value)) {
                case 'true':
                case '(true)':
                    return true;
                case 'false':
                case '(false)':
                    return false;
                case 'empty':
                case '(empty)':
                    return '';
                case 'null':
                case '(null)':
                    return null;
            }
        }
        
        return $value;
    }
}

// Define constants from environment variables
define('ENVIRONMENT', env('APP_ENV', 'development'));
define('APP_NAME', env('APP_NAME', 'Health and Safety'));
define('APP_URL', env('APP_URL'));
define('APP_ROOT', dirname(__DIR__));
define('APP_DEBUG', env('APP_DEBUG', false));

// Database Configuration
define('DB_HOST', env('DB_HOST'));
define('DB_PORT', env('DB_PORT', 3306));
define('DB_NAME', env('DB_NAME'));
define('DB_USER', env('DB_USER'));
define('DB_PASS', env('DB_PASS'));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

// Security Keys
define('SECURE_AUTH_KEY', env('SECURE_AUTH_KEY'));
define('SECURE_AUTH_SALT', env('SECURE_AUTH_SALT'));

// Session Configuration
define('SESSION_NAME', env('SESSION_NAME', 'h_s_inventory_session'));
define('SESSION_LIFETIME', (int)env('SESSION_LIFETIME', 604800));
define('SESSION_PATH', env('SESSION_PATH', '/'));
define('SESSION_DOMAIN', env('SESSION_DOMAIN', ''));
define('SESSION_SECURE', env('SESSION_SECURE', ENVIRONMENT === 'production'));
define('SESSION_HTTPONLY', env('SESSION_HTTPONLY', true));

// Timezone
date_default_timezone_set(env('APP_TIMEZONE', 'America/New_York'));

// Error Handling
$errorLevel = env('ERROR_REPORTING', 'E_ALL');

// Convert string error level to constant
switch (strtoupper($errorLevel)) {
    case 'E_ALL':
        $errorReporting = E_ALL;
        break;
    case 'E_ERROR':
        $errorReporting = E_ERROR;
        break;
    case 'E_WARNING':
        $errorReporting = E_ERROR | E_WARNING;
        break;
    case 'E_NOTICE':
        $errorReporting = E_ERROR | E_WARNING | E_NOTICE;
        break;
    case 'E_STRICT':
        $errorReporting = E_ALL | E_STRICT;
        break;
    case 'E_DEPRECATED':
        $errorReporting = E_ALL | E_DEPRECATED;
        break;
    case 'NONE':
        $errorReporting = 0;
        break;
    default:
        $errorReporting = E_ALL; // Default to all errors
}

// Set error reporting level
error_reporting($errorReporting);

// Display errors (only in debug mode)
ini_set('display_errors', APP_DEBUG ? 1 : 0);
ini_set('display_startup_errors', APP_DEBUG ? 1 : 0);

// Always log errors
ini_set('log_errors', 1);
ini_set('error_log', APP_ROOT . '/logs/error.log');

// Log all types of errors
ini_set('error_reporting', $errorReporting);

// File Upload Settings
define('MAX_FILE_SIZE', (int)env('MAX_FILE_SIZE', 5242880));
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);
define('UPLOAD_PATH', APP_ROOT . env('UPLOAD_PATH', '/public/uploads/'));

// Feature Flags
define('ENABLE_PASSWORD_RESET', env('ENABLE_PASSWORD_RESET', true));
define('ENABLE_AUDIT_LOG', env('ENABLE_AUDIT_LOG', true));
define('ENABLE_LDAP', env('ENABLE_LDAP', false));

// LDAP Configuration
define('LDAP_ENABLED', extension_loaded('ldap') && ENABLE_LDAP);
if (ENABLE_LDAP) {
    define('LDAP_HOST', env('LDAP_HOST'));
    define('LDAP_PORT', env('LDAP_PORT', 389));
    define('LDAP_DOMAIN', env('LDAP_DOMAIN'));
    define('LDAP_BASE_DN', env('LDAP_BASE_DN'));
    define('LDAP_GROUP', env('LDAP_GROUP'));
    define('LDAP_GROUP_DN', env('LDAP_GROUP_DN'));
}

// Email Configuration
define('ADMIN_EMAIL', env('ADMIN_EMAIL', 'admin@example.com'));
define('SYSTEM_EMAIL', env('SYSTEM_EMAIL', 'noreply@example.com'));

// Maintenance Mode
define('MAINTENANCE_MODE', env('MAINTENANCE_MODE', false));
define('MAINTENANCE_MESSAGE', env('MAINTENANCE_MESSAGE', 'The system is currently under maintenance. Please check back later.'));

// API Settings
define('API_RATE_LIMIT', (int)env('API_RATE_LIMIT', 100));
define('API_KEY_REQUIRED', env('API_KEY_REQUIRED', false));

// Backup Settings
define('BACKUP_ENABLED', env('BACKUP_ENABLED', true));
define('BACKUP_PATH', APP_ROOT . env('BACKUP_PATH', '/backups/'));
define('BACKUP_RETENTION_DAYS', (int)env('BACKUP_RETENTION_DAYS', 30));

// Logging Settings
define('LOG_ROTATION', env('LOG_ROTATION', true));
define('LOG_MAX_FILES', (int)env('LOG_MAX_FILES', 30));

// Advanced Features
define('ENABLE_TWO_FACTOR_AUTH', env('ENABLE_TWO_FACTOR_AUTH', false));
define('ENABLE_PERFORMANCE_MONITORING', env('ENABLE_PERFORMANCE_MONITORING', false));
define('SLOW_QUERY_THRESHOLD', (int)env('SLOW_QUERY_THRESHOLD', 1000));

// Installation marker
define('INSTALLATION_COMPLETED', true);
