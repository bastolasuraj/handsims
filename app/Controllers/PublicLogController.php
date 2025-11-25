<?php

namespace App\Controllers;

use App\Core\Controller;

class PublicLogController extends Controller
{
    public function index()
    {
        // This version doesn't require authentication for testing
        // Remove this controller in production!

        $logModel = $this->model('LogModel');
        // Handle search
        $searchTerm = $_GET['search'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        if ($searchTerm || $dateFrom || $dateTo) {
            $logs = $logModel->searchLogs($searchTerm, $dateFrom, $dateTo);
        } else {
            $logs = $logModel->getLogs($limit, $offset);
        }

        // Format logs for display
        $formattedLogs = $this->formatLogs($logs);
        // Set dummy session data for the view
        $_SESSION['role'] = 'admin';
        $_SESSION['username'] = 'Guest Viewer';
        $data = [
            'logs' => $logs,
            'formatted_logs' => $formattedLogs,
            'search' => $searchTerm,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'page' => $page
        ];
        $this->view('logs/index', $data);
    }

    private function formatLogs($logs)
    {
        $formatted = [];
        foreach ($logs as $log) {
            $timestamp = date('Y-m-d H:i:s', strtotime($log['created_at']));
            $text = $timestamp . " - " . $log['username'] . " " . $log['action'];
            if ($log['details']) {
                $text .= ": " . $log['details'];
            }

            $formatted[] = $text;
        }

        return $formatted;
    }
}
