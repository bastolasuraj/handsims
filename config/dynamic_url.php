<?php
/**
 * Dynamic URL Detection
 * This file dynamically determines the application URL based on the current request
 */

function getDynamicAppUrl() {
    // Determine protocol (http or https)
    $protocol = 'http';
    
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        $protocol = 'https';
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $protocol = 'https';
    } elseif (isset($_SERVER['REQUEST_SCHEME'])) {
        $protocol = $_SERVER['REQUEST_SCHEME'];
    }
    
    // Get the host (domain or IP)
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    
    // Get the base path (directory where the app is installed)
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath = dirname($scriptName);

    // Normalize directory separator and handle root install
    if ($basePath === '/' || $basePath === '\\') {
        $basePath = '';
    }
    
    // Construct the full URL
    $appUrl = $protocol . '://' . $host . $basePath;
    
    // Remove trailing slash if present
    $appUrl = rtrim($appUrl, '/');
    
    return $appUrl;
}

// Define APP_URL dynamically if not already defined
if (!defined('APP_URL')) {
    define('APP_URL', getDynamicAppUrl());
}

// Also create a function that can be called to get the current URL
function getCurrentAppUrl() {
    return getDynamicAppUrl();
}
?>