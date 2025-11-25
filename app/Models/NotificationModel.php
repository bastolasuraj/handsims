<?php

namespace App\Models;

use App\Core\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';

    public function addNotification($userId, $message, $type = 'activity', $productId = null, $locationId = null, $sizeId = null)
    {
        $sql = "INSERT INTO notifications (user_id, message, type, product_id, location_id, size_id) VALUES (:user_id, :message, :type, :product_id, :location_id, :size_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(
            [
            'user_id' => $userId,
            'message' => $message,
            'type' => $type,
            'product_id' => $productId,
            'location_id' => $locationId,
            'size_id' => $sizeId
            ]
        );
    }

    public function getUnreadNotifications($userId)
    {
        $sql = "SELECT * FROM notifications WHERE user_id = :user_id AND is_read = 0 ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function markAsRead($notificationId)
    {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $notificationId]);
    }

    public function markAllAsRead($userId)
    {
        $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['user_id' => $userId]);
    }
}
