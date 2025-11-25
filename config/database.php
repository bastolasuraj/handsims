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
    
    return; // Stop processing the rest of the file
}

// Only define charset if not already defined
if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}