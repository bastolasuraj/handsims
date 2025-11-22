<?php

// Security Headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Load configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';

// Initialize Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Initialize session
SessionManager::init();

// Register error handler
if (file_exists(__DIR__ . '/app/Core/ErrorHandler.php')) {
    require_once __DIR__ . '/app/Core/ErrorHandler.php';
    \App\Core\ErrorHandler::register();
}

// Load middleware
if (file_exists(__DIR__ . '/app/Middleware/EnvGuard.php')) {
    require_once __DIR__ . '/app/Middleware/EnvGuard.php';
}

// Initialize router
$router = new App\Core\Router();

// Define routes
$router->addRoute('/', 'DashboardController', 'index');
$router->addRoute('/dashboard', 'DashboardController', 'index');
$router->addRoute('/dashboard/seed-demo', 'DashboardController', 'seedDemo');

// Stock routes
$router->addRoute('/stock/add', 'StockController', 'add');
$router->addRoute('/stock/out', 'StockController', 'out');
$router->addRoute('/stock/transfer', 'StockController', 'transfer');
$router->addRoute('/stock/in-stock', 'StockController', 'inStock');
$router->addRoute('/stock/getAvailableSizesInStock', 'StockController', 'getAvailableSizesInStock');
$router->addRoute('/stock/getInventoryJson', 'StockController', 'getInventoryJson');
$router->addRoute('/stock/getProductSizes', 'StockController', 'getProductSizes');
$router->addRoute('/stock/check-stock', 'StockController', 'checkStock'); // New route
$router->addRoute('/stock/bulkAdd', 'StockController', 'bulkAdd');

// Product routes
$router->addRoute('/products', 'ProductController', 'index');
$router->addRoute('/products/add', 'ProductController', 'add');
$router->addRoute('/products/edit', 'ProductController', 'edit');
$router->addRoute('/products/search-by-barcode', 'ProductController', 'searchByBarcode'); // New route
$router->addRoute('/products/bulkAdd', 'ProductController', 'bulkAdd');
$router->addRoute('/products/detail', 'ProductController', 'detail');
$router->addRoute('/products/delete', 'ProductController', 'delete');

// Report routes
$router->addRoute('/reports', 'ReportController', 'index');
$router->addRoute('/reports/generate', 'ReportController', 'generate');

// Log routes
$router->addRoute('/activity-logs', 'LogController', 'index');
$router->addRoute('/activity-logs/export', 'LogController', 'export');
$router->addRoute('/activity-logs/clear', 'LogController', 'clear');

// Notification routes
$router->addRoute('/notifications', 'NotificationController', 'index');
$router->addRoute('/notifications/markAsRead', 'NotificationController', 'markAsRead');
$router->addRoute('/notifications/markAllAsRead', 'NotificationController', 'markAllAsRead');

// Config/Master Data routes
$router->addRoute('/master-data', 'ConfigController', 'index');
$router->addRoute('/master-data/categories', 'ConfigController', 'categories');
$router->addRoute('/master-data/sizes', 'ConfigController', 'sizes');
$router->addRoute('/master-data/locations', 'ConfigController', 'locations');
$router->addRoute('/master-data/departments', 'ConfigController', 'departments');
$router->addRoute('/master-data/sellers', 'ConfigController', 'sellers');
$router->addRoute('/master-data/types', 'ConfigController', 'types');

// Settings routes
$router->addRoute('/settings/toast-templates', 'ToastSettingsController', 'index');
$router->addRoute('/settings/toast-templates/update', 'ToastSettingsController', 'update');
$router->addRoute('/settings/toast-templates/reset', 'ToastSettingsController', 'reset');
$router->addRoute('/settings/toast-templates/reset-all', 'ToastSettingsController', 'resetAll');

// Database viewer (local users only)
$router->addRoute('/settings/database', 'DatabaseController', 'index');
$router->addRoute('/settings/database/table', 'DatabaseController', 'table');

// Public log viewer
$router->addRoute('/public-logs', 'PublicLogController', 'index');

// Auth routes
$router->addRoute('/login', 'AuthController', 'login');
$router->addRoute('/logout', 'AuthController', 'logout');
$router->addRoute('/register', 'AuthController', 'register');

// Dashboard routes (for AJAX)
$router->addRoute('/dashboard/get-stats', 'DashboardController', 'getStats'); // New route

// Get the current URL path
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// Remove query string
$requestUri = strtok($requestUri, '?');

// Find the position of '/v4/' in the request URI
$v4_pos = strpos($requestUri, '/v4/');

if ($v4_pos !== false) {
    // Extract the path starting from /v4/
    $url = substr($requestUri, $v4_pos + 3); // +3 to remove "/v4"
} else {
    // Fallback for when /v4/ is not in the URL, or for root access like /v4
    $url = $requestUri;
    if ($url === '/v4') $url = '/';
}
// Ensure URL starts with /
if (empty($url)) {
    $url = '/';
} elseif ($url[0] !== '/') {
    $url = '/' . $url;
}

// Remove trailing slash except for root
if ($url !== '/' && substr($url, -1) === '/') {
    $url = rtrim($url, '/');
}

// Route the request
$router->route($url);
?>