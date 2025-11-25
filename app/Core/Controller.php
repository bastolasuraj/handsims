<?php

namespace App\Core;

use App\Core\Database;

// Base Controller class
class Controller
{
    protected $db;
    public $notifications = [];

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Load model
    protected function model($model)
    {
        $modelClass = 'App\\Models\\' . $model;
        if (class_exists($modelClass)) {
            return new $modelClass($this->db);
        }
        throw new \Exception("Model not found: " . $modelClass);
    }

    // Load view
    protected function view($view, $data = [], $useLayout = true)
    {
        // Load notifications for logged-in users
        if (isset($_SESSION['user_id']) && $useLayout) {
            $notificationModel = $this->model('NotificationModel');
            $this->notifications = $notificationModel->getUnreadNotifications($_SESSION['user_id']);

            // Prioritize inventory notifications
            usort(
                $this->notifications, function ($a, $b) {
                    if ($a['type'] == 'inventory' && $b['type'] != 'inventory') {
                        return -1;
                    }
                    if ($a['type'] != 'inventory' && $b['type'] == 'inventory') {
                        return 1;
                    }
                    return strtotime($b['created_at']) - strtotime($a['created_at']);
                }
            );
        }

        $data['notifications'] = $this->notifications;
        extract($data);

        if ($useLayout) {
            // Start output buffering for content
            ob_start();
            require_once APP_ROOT . '/app/views/' . $view . '.php';
            $content = ob_get_clean();

            // Load layout with content
            require_once APP_ROOT . '/app/views/layouts/main.php';
        } else {
            // Render view directly without layout (for AJAX/partials)
            require_once APP_ROOT . '/app/views/' . $view . '.php';
        }
    }

    // Load partial view (without layout)
    protected function partial($view, $data = [])
    {
        $this->view($view, $data, false);
    }

    // Redirect helper
    protected function redirect($url)
    {
        header("Location: " . APP_URL . $url);
        exit();
    }

    // Check if user is logged in
    protected function requireAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            // Capture the current URL to redirect back after login
            $currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
            // Remove the base path if present to get the relative URL
            $basePath = parse_url(APP_URL, PHP_URL_PATH) ?? '';
            if ($basePath && strpos($currentUrl, $basePath) === 0) {
                $currentUrl = substr($currentUrl, strlen($basePath));
            }
            // Redirect to login with returnTo parameter
            $this->redirect('/login?returnTo=' . urlencode($currentUrl));
        }
    }

    // Add log entry
    protected function addLog($action, $details = '')
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $logModel = $this->model('LogModel');
        $userId = $_SESSION['user_id'] ?? null;
        $username = $_SESSION['username'] ?? 'System';

        // In a real-world scenario, you would get the user's public IP address
        // This might involve a service call or checking server variables
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;

        // Handle multiple IPs in X-Forwarded-For and truncate to fit varchar(45)
        if ($ip) {
            $ips = explode(',', $ip);
            $ip = trim($ips[0]); // Take the first IP address
            $ip = substr($ip, 0, 45); // Truncate to 45 characters
        }

        $logModel->addLog($userId, $username, $action, $details, $ip);
        // Don't create notification for every log - only for important events
    }

    // Add notification entry
    protected function addNotification($userId, $message, $type = 'activity')
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $notificationModel = $this->model('NotificationModel');
        $notificationModel->addNotification($userId, $message, $type);
    }

    // JSON response helper
    protected function json($data)
    {
        header("HTTP/1.1 200 OK");
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
