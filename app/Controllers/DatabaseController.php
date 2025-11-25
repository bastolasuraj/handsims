<?php

namespace App\Controllers;

use App\Core\Controller;
use PDO;

class DatabaseController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        
        // SECURITY: Only allow local users to access database viewer
        if (!$this->isLocalUser()) {
            $_SESSION['error'] = 'Access denied. This feature is only available for local users.';
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
    }

    /**
     * Check if current user is a local user (not LDAP/AD)
     */
    private function isLocalUser()
    {
        $sql = "SELECT auth_type FROM users WHERE id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user && $user['auth_type'] === 'local';
    }

    /**
     * Display database viewer page
     */
    public function index()
    {
        try {
            // Get all tables
            $sql = "SHOW TABLES";
            $stmt = $this->db->query($sql);
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $tableData = [];
            
            foreach ($tables as $table) {
                // Get table structure
                $sql = "DESCRIBE `$table`";
                $stmt = $this->db->query($sql);
                $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get row count
                $sql = "SELECT COUNT(*) as count FROM `$table`";
                $stmt = $this->db->query($sql);
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                
                // Get sample data (first 100 rows)
                $sql = "SELECT * FROM `$table` LIMIT 100";
                $stmt = $this->db->query($sql);
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $tableData[] = [
                    'name' => $table,
                    'structure' => $structure,
                    'count' => $count,
                    'data' => $data
                ];
            }
            
            $this->view(
                'settings/database', [
                'title' => 'Database Viewer',
                'tables' => $tableData
                ]
            );
            
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error loading database: ' . $e->getMessage();
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
    }

    /**
     * Get specific table data with pagination
     */
    public function table()
    {
        $tableName = $_GET['name'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 50;
        $offset = ($page - 1) * $perPage;
        
        if (empty($tableName)) {
            $_SESSION['error'] = 'Table name is required';
            header('Location: ' . APP_URL . '/settings/database');
            exit;
        }
        
        try {
            // Validate table exists
            $sql = "SHOW TABLES LIKE :table";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['table' => $tableName]);
            
            if (!$stmt->fetch()) {
                $_SESSION['error'] = 'Table not found';
                header('Location: ' . APP_URL . '/settings/database');
                exit;
            }
            
            // Get table structure
            $sql = "DESCRIBE `$tableName`";
            $stmt = $this->db->query($sql);
            $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get total count
            $sql = "SELECT COUNT(*) as count FROM `$tableName`";
            $stmt = $this->db->query($sql);
            $totalCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Get paginated data
            $sql = "SELECT * FROM `$tableName` LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalPages = ceil($totalCount / $perPage);
            
            $this->view(
                'settings/database-table', [
                'title' => 'Table: ' . $tableName,
                'tableName' => $tableName,
                'structure' => $structure,
                'data' => $data,
                'totalCount' => $totalCount,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'perPage' => $perPage
                ]
            );
            
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error loading table: ' . $e->getMessage();
            header('Location: ' . APP_URL . '/settings/database');
            exit;
        }
    }
}
