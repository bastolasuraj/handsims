<?php
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Forbidden');
}

/**
 * Cron Job: Check Inventory Levels
 * Run this script periodically (e.g., every hour) to check for low stock and create notifications
 * 
 * Usage: php cron_check_inventory.php
 * Or set up as a cron job: 0 * * * * /usr/bin/php /path/to/cron_check_inventory.php
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/Helpers/NotificationHelper.php';

try {
    $db = Database::getInstance()->getConnection();
    $notificationHelper = new NotificationHelper($db);
    
    echo "[" . date('Y-m-d H:i:s') . "] Checking inventory levels...\n";
    
    // Check inventory and create notifications for low/critical stock
    $notificationHelper->checkInventoryLevels();
    
    echo "[" . date('Y-m-d H:i:s') . "] Inventory check complete.\n";
    
    // Clean up old read notifications (older than 30 days)
    $sql = "DELETE FROM notifications WHERE is_read = 1 AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $stmt = $db->prepare($sql);
    $deleted = $stmt->execute();
    $count = $stmt->rowCount();
    
    if ($count > 0) {
        echo "[" . date('Y-m-d H:i:s') . "] Cleaned up {$count} old notifications.\n";
    }
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
