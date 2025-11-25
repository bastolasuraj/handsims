<?php
/**
 * Simple PHPUnit bootstrap that defines the handful of globals
 * referenced by controllers and loads the Composer autoloader.
 */

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

if (!defined('APP_URL')) {
    define('APP_URL', '');
}

require APP_ROOT . '/vendor/autoload.php';
