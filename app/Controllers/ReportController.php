<?php

namespace App\Controllers;

use App\Core\Controller;

class ReportController extends Controller
{
    public function index()
    {
        $this->requireAuth();
        $data = [
            'locations' => $this->getLocations(),
            'departments' => $this->getDepartments(),
            'users' => $this->getUsers()
        ];
        $this->view('reports/index', $data);
    }

    public function generate()
    {
        $this->requireAuth();
        $reportType = $_POST['report_type'] ?? 'inventory';
        $format = $_POST['format'] ?? 'csv';
        $filters = [
            'location_id' => $_POST['location_id'] ?? null,
            'department_id' => $_POST['department_id'] ?? null,
            'user_id' => $_POST['user_id'] ?? null,
            'date_from' => $_POST['date_from'] ?? null,
            'date_to' => $_POST['date_to'] ?? null
        ];
        switch ($reportType) {
            case 'inventory':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      $data = $this->generateInventoryReport($filters);

                break;
            case 'transactions':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  $data = $this->generateTransactionReport($filters);

                break;
            case 'low_stock':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  $data = $this->generateLowStockReport($filters);

                break;
            case 'activity':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  $data = $this->generateActivityReport($filters);

                break;
            default:
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  $data = [];
        }

        if ($format === 'csv') {
            $this->exportCSV($data, $reportType);
        } else {
            $this->exportPDF($data, $reportType);
        }
    }

    private function generateInventoryReport($filters)
    {
        $sql = "SELECT 
                p.part_number,
                t.name as product_type,
                c.name as category,
                l.name as location,
                ps.size,
                i.quantity,
                i.min_quantity,
                i.remarks,
                i.last_updated
                FROM inventory i
                JOIN products p ON i.product_id = p.id
                JOIN locations l ON i.location_id = l.id
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN product_sizes ps ON i.size_id = ps.id
                LEFT JOIN types t ON p.type_id = t.id
                WHERE 1=1";
        $params = [];
        if ($filters['location_id']) {
            $sql .= " AND i.location_id = :location_id";
            $params['location_id'] = $filters['location_id'];
        }

        $sql .= " ORDER BY l.name, p.part_number, ps.size";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $this->addLog("Report generated", "Type: Inventory Report");
        return $stmt->fetchAll();
    }

    private function generateTransactionReport($filters)
    {
        $transactionModel = $this->model('TransactionModel');
        $data = $transactionModel->getTransactionHistory($filters);
        $this->addLog("Report generated", "Type: Transaction Report");
        return $data;
    }

    private function generateLowStockReport($filters)
    {
        $inventoryModel = $this->model('InventoryModel');
        $data = $inventoryModel->getLowStockItems();
        $this->addLog("Report generated", "Type: Low Stock Report");
        return $data;
    }

    private function generateActivityReport($filters)
    {
        $sql = "SELECT 
                al.created_at,
                al.username,
                al.action,
                al.details,
                al.ip
                FROM activity_logs al
                WHERE 1=1";
        $params = [];
        if ($filters['user_id']) {
            $sql .= " AND al.user_id = :user_id";
            $params['user_id'] = $filters['user_id'];
        }

        if ($filters['date_from']) {
            $sql .= " AND DATE(al.created_at) >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if ($filters['date_to']) {
            $sql .= " AND DATE(al.created_at) <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        $sql .= " ORDER BY al.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $this->addLog("Report generated", "Type: Activity Report");
        return $stmt->fetchAll();
    }

    private function exportCSV($data, $filename)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '_' . date('Y-m-d_H-i-s') . '.csv"');
        $output = fopen('php://output', 'w');
// Add headers
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
        }

        // Add data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit();
    }

    private function exportPDF($data, $reportType)
    {
        // For now, we'll create a simple HTML table that can be printed as PDF
        // In production, you would use a library like TCPDF or mPDF

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>' . ucfirst($reportType) . ' Report</title>
            <style>
                body { font-family: Arial, sans-serif; }
                h1 { color: #333; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th { background-color: #4CAF50; color: white; padding: 12px; text-align: left; }
                td { padding: 8px; border-bottom: 1px solid #ddd; }
                tr:nth-child(even) { background-color: #f2f2f2; }
                .header { margin-bottom: 20px; }
                .footer { margin-top: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>' . ucfirst($reportType) . ' Report</h1>
                <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
                <p>Generated by: ' . $_SESSION['username'] . '</p>
            </div>
            <table>';
// Add headers
        if (!empty($data)) {
            $html .= '<tr>';
            foreach (array_keys($data[0]) as $header) {
                $html .= '<th>' . ucwords(str_replace('_', ' ', $header)) . '</th>';
            }
            $html .= '</tr>';
        }

        // Add data
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $value) {
                $html .= '<td>' . htmlspecialchars($value ?? '') . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table>
            <div class="footer">
                <p>H&S Inventory Management System - Confidential</p>
            </div>
            <script>window.print();</script>
        </body>
        </html>';
        echo $html;
        exit();
    }

    private function getLocations()
    {
        $sql = "SELECT * FROM locations ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getDepartments()
    {
        $sql = "SELECT * FROM departments ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getUsers()
    {
        $sql = "SELECT id, username, full_name FROM users ORDER BY username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
