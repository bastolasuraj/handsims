<?php

namespace App\Helpers;

use App\Models\NotificationModel;

/**
 * Notification Helper
 * Creates smart, contextual notifications for important events
 */

class NotificationHelper
{
    private $db;
    private $notificationModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->notificationModel = new NotificationModel($db);
    }

    /**
     * Check inventory levels and create notifications for low/critical stock
     */
    public function checkInventoryLevels()
    {
        $sql = "SELECT 
                    i.id,
                    i.product_id,
                    i.quantity,
                    p.part_number,
                    p.product_type,
                    p.low_stock_threshold,
                    s.size,
                    l.name as location_name,
                    CASE 
                        WHEN i.quantity = 0 THEN 'out_of_stock'
                        WHEN i.quantity <= (p.low_stock_threshold * 0.5) THEN 'critical'
                        WHEN i.quantity <= p.low_stock_threshold THEN 'low'
                        ELSE 'normal'
                    END as stock_status
                FROM inventory i
                JOIN products p ON i.product_id = p.id
                LEFT JOIN product_sizes s ON i.size_id = s.id
                JOIN locations l ON i.location_id = l.id
                WHERE i.quantity <= p.low_stock_threshold
                AND p.low_stock_threshold > 0
                ORDER BY stock_status DESC, i.quantity ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $items = $stmt->fetchAll();

        foreach ($items as $item) {
            $this->createInventoryAlert($item);
        }
    }

    /**
     * Create inventory alert notification
     */
    private function createInventoryAlert($item)
    {
        $productName = $item['product_type'] ?: $item['part_number'];
        $size = $item['size'] ? " ({$item['size']})" : '';
        $location = $item['location_name'];
        $quantity = $item['quantity'];
        $minQty = $item['low_stock_threshold'];

        // Check if notification already exists for this item (within last 24 hours)
        $checkSql = "SELECT id FROM notifications 
                     WHERE message LIKE :pattern 
                     AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
                     AND is_read = 0
                     LIMIT 1";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute(['pattern' => "%{$productName}%{$location}%"]);

        if ($checkStmt->fetch()) {
            return; // Don't create duplicate notification
        }

        $message = '';
        $type = 'inventory';

        switch ($item['stock_status']) {
        case 'out_of_stock':
            $message = "⚠️ OUT OF STOCK: {$productName}{$size} at {$location}. Restock immediately!";
            break;
        case 'critical':
            $message = "🔴 CRITICAL: Only {$quantity} units of {$productName}{$size} left at {$location} (Min: {$minQty})";
            break;
        case 'low':
            $message = "🟡 LOW STOCK: {$productName}{$size} at {$location} has {$quantity} units (Min: {$minQty})";
            break;
        }

        if ($message) {
            // Notify all users
            $usersSql = "SELECT id FROM users WHERE is_active = 1";
            $usersStmt = $this->db->prepare($usersSql);
            $usersStmt->execute();
            $users = $usersStmt->fetchAll();

            foreach ($users as $user) {
                $this->notificationModel->addNotification($user['id'], $message, $type);
            }
        }
    }

    /**
     * Create notification for stock added
     */
    public function notifyStockAdded($userId, $username, $productName, $size, $quantity, $location)
    {
        $sizeText = $size ? " ({$size})" : '';
        $message = "✅ {$username} added {$quantity} units of {$productName}{$sizeText} to {$location}";

        // Only notify other users, not the one who performed the action
        $this->notifyOtherUsers($userId, $message, 'activity');
    }

    /**
     * Create notification for stock removed
     */
    public function notifyStockRemoved($userId, $username, $productName, $size, $quantity, $location, $department)
    {
        $sizeText = $size ? " ({$size})" : '';
        $deptText = $department ? " for {$department}" : '';
        $message = "📤 {$username} removed {$quantity} units of {$productName}{$sizeText} from {$location}{$deptText}";

        $this->notifyOtherUsers($userId, $message, 'activity');
    }

    /**
     * Create notification for stock transfer
     */
    public function notifyStockTransferred($userId, $username, $productName, $size, $quantity, $fromLocation, $toLocation)
    {
        $sizeText = $size ? " ({$size})" : '';
        $message = "🔄 {$username} transferred {$quantity} units of {$productName}{$sizeText} from {$fromLocation} to {$toLocation}";

        $this->notifyOtherUsers($userId, $message, 'activity');
    }

    /**
     * Create notification for new product added
     */
    public function notifyProductAdded($userId, $username, $productName, $partNumber)
    {
        $message = "🆕 {$username} added new product: {$productName} ({$partNumber})";

        $this->notifyOtherUsers($userId, $message, 'system');
    }

    /**
     * Create notification for product deleted
     */
    public function notifyProductDeleted($userId, $username, $productName)
    {
        $message = "🗑️ {$username} deleted product: {$productName}";

        $this->notifyOtherUsers($userId, $message, 'system');
    }

    /**
     * Notify all users except the one who performed the action
     */
    private function notifyOtherUsers($excludeUserId, $message, $type)
    {
        $sql = "SELECT id FROM users WHERE is_active = 1 AND id != :exclude_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['exclude_id' => $excludeUserId]);
        $users = $stmt->fetchAll();

        foreach ($users as $user) {
            $this->notificationModel->addNotification($user['id'], $message, $type);
        }
    }

    /**
     * Notify specific user
     */
    public function notifyUser($userId, $message, $type = 'system')
    {
        $this->notificationModel->addNotification($userId, $message, $type);
    }

    /**
     * Notify all users
     */
    public function notifyAllUsers($message, $type = 'system')
    {
        $sql = "SELECT id FROM users WHERE is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll();

        foreach ($users as $user) {
            $this->notificationModel->addNotification($user['id'], $message, $type);
        }
    }
}
