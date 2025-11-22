<?php

namespace App\Controllers;

use App\Core\Controller;

class NotificationController extends Controller
{
    protected $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->notificationModel = $this->model('NotificationModel');
    }

    public function index()
    {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $notifications = $this->notificationModel->getUnreadNotifications($userId);

        // Prioritize inventory notifications
        usort($notifications, function ($a, $b) {
            if ($a['type'] == 'inventory' && $b['type'] != 'inventory') {
                return -1;
            }
            if ($a['type'] != 'inventory' && $b['type'] == 'inventory') {
                return 1;
            }
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        header('Content-Type: application/json');
        $this->json($notifications);
    }

    public function markAsRead()
    {
        $this->requireAuth();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->notificationModel->markAsRead($id);
        }
        $this->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $this->notificationModel->markAllAsRead($userId);
        $this->json(['success' => true]);
    }
}
