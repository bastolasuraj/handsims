<?php

namespace App\Controllers;

use App\Core\Controller;
use Exception;

class DashboardController extends Controller
{
    public function index()
    {
        $this->requireAuth();

        // Get models
        $inventoryModel = $this->model('InventoryModel');
        $productModel = $this->model('ProductModel');
        $transactionModel = $this->model('TransactionModel');
        $logModel = $this->model('LogModel');
        $notificationModel = $this->model('NotificationModel');

        $this->notifications = $notificationModel->getUnreadNotifications($_SESSION['user_id']);

        // Get dashboard statistics
        $data = [
            'total_products' => $productModel->count(),
            'recent_transactions' => $transactionModel->getTransactionHistory(['limit' => 10]),
            'recent_logs' => $logModel->getLogs(5),
            'stock_by_location' => $this->getStockByLocation($inventoryModel)
        ];

        $this->view('dashboard/index', $data);
    }

    public function seedDemo()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
        }

        // Check if user has admin role - temporarily disabled for testing
        // Uncomment the following lines to restrict to admin users only
        /*
        $userRole = $_SESSION['role'] ?? 'user';
        if ($userRole !== 'admin' && $userRole !== 'Admin') {
            $_SESSION['error'] = 'Only administrators can generate demo data';
            $this->redirect('/');
        }
        */

        // Check if a transaction is already active and commit it first
        if ($this->db->inTransaction()) {
            $this->db->commit();
        }

        $this->db->beginTransaction();
        try {
            $productModel = $this->model('ProductModel');
            $inventoryModel = $this->model('InventoryModel');
            $transactionModel = $this->model('TransactionModel');

            // Ensure we have base lookup data
            $categories = $this->db->query("SELECT id FROM categories")->fetchAll();
            if (empty($categories)) {
                $this->db->exec("INSERT INTO categories (name, type) VALUES ('General', 'General')");
                $categories = $this->db->query("SELECT id FROM categories")->fetchAll();
            }
            $locations = $this->db->query("SELECT id FROM locations")->fetchAll();
            if (empty($locations)) {
                $this->db->exec("INSERT INTO locations (name) VALUES ('Main Warehouse'), ('Secondary Warehouse')");
                $locations = $this->db->query("SELECT id FROM locations")->fetchAll();
            }
            $sizes = $this->db->query("SELECT id FROM product_sizes")->fetchAll();
            if (empty($sizes)) {
                $this->db->exec("INSERT INTO product_sizes (size) VALUES ('S'), ('M'), ('L'), ('XL'), ('One Size')");
                $sizes = $this->db->query("SELECT id FROM product_sizes")->fetchAll();
            }
            $departments = $this->db->query("SELECT id FROM departments")->fetchAll();
            if (empty($departments)) {
                $this->db->exec("INSERT INTO departments (name) VALUES ('Sales'), ('Service'), ('Management'), ('HR')");
                $departments = $this->db->query("SELECT id FROM departments")->fetchAll();
            }
            $sellers = $this->db->query("SELECT id FROM sellers")->fetchAll();
            if (empty($sellers)) {
                $this->db->exec("INSERT INTO sellers (name) VALUES ('ABC Supplies'), ('XYZ Inc.'), ('Global Corp.')");
                $sellers = $this->db->query("SELECT id FROM sellers")->fetchAll();
            }
            $types = $this->db->query("SELECT id FROM types")->fetchAll();
            if (empty($types)) {
                $this->db->exec(
                    "INSERT INTO types (name, description) VALUES 
                    ('Type A', 'Product Type A'),
                    ('Type B', 'Product Type B'),
                    ('Type C', 'Product Type C'),
                    ('Type D', 'Product Type D'),
                    ('Type E', 'Product Type E')"
                );
                $types = $this->db->query("SELECT id FROM types")->fetchAll();
            }

            // Create at least 100 products if fewer exist
            $existingCount = $productModel->count();
            $toCreate = max(0, 100 - (int)$existingCount);
            if ($toCreate > 0) {
                $catIds = array_column($categories, 'id');
                $sizeIds = array_column($sizes, 'id');
                $typeIds = array_column($types, 'id');
                for ($i = 1; $i <= $toCreate; $i++) {
                    $partNumber = 'PN-' . str_pad((string)($existingCount + $i), 5, '0', STR_PAD_LEFT);

                    // Assign 1 to 5 random sizes
                    $numSizes = rand(1, 5);
                    $productSizeIds = [];
                    for ($j = 0; $j < $numSizes; $j++) {
                        $productSizeIds[] = $sizeIds[array_rand($sizeIds)];
                    }
                    $productSizeIds = array_unique($productSizeIds);
                    $availableSizes = implode(',', $productSizeIds);

                    $data = [
                        'part_number' => $partNumber,
                        'product_type' => 'Type ' . (($existingCount + $i) % 5 + 1),
                        'category_id' => $catIds[array_rand($catIds)],
                        'type_id' => $typeIds[array_rand($typeIds)],
                        'description' => 'Demo product ' . $partNumber,
                        'low_stock_threshold' => 10,
                        'qr_code' => 'QR_' . $partNumber . '_' . time(),
                        'available_sizes' => $availableSizes
                    ];
                    $productModel->create($data);
                }
            }

            // Inventory: ensure each product has entries across locations and sizes
            $products = $productModel->getAll();
            $locIds = array_column($locations, 'id');
            $deptIds = array_column($departments, 'id');
            $sellerIds = array_column($sellers, 'id');

            foreach ($products as $p) {
                // create 2-4 inventory entries per product
                $entries = rand(2, 4);
                for ($e = 0; $e < $entries; $e++) {
                    $locationId = $locIds[array_rand($locIds)];

                    $productSizes = explode(',', $p['available_sizes']);
                    if (empty($productSizes[0])) {
                        continue;
                    }
                    $sizeId = $productSizes[array_rand($productSizes)];

                    $qty = rand(5, 50);
                    $inventoryModel->updateStock($p['id'], $locationId, $sizeId, $qty, 'add');

                    // Add matching IN transaction
                    $transactionModel->addTransaction(
                        [
                        'transaction_type' => 'IN',
                        'product_id' => $p['id'],
                        'location_id' => $locationId,
                        'department_id' => null,
                        'size_id' => $sizeId,
                        'quantity' => $qty,
                        'user_id' => $_SESSION['user_id'] ?? null,
                        'remarks' => 'Demo seed IN',
                        'seller_id' => $sellerIds[array_rand($sellerIds)],
                        'price_per_unit' => rand(10, 1000) / 10
                        ]
                    );

                    // Add some OUT transactions
                    if (rand(1, 3) == 1) {
                        $outQty = rand(1, 5);
                        if ($qty > $outQty) {
                            $inventoryModel->updateStock($p['id'], $locationId, $sizeId, $outQty, 'subtract');
                            $transactionModel->addTransaction(
                                [
                                'transaction_type' => 'OUT',
                                'product_id' => $p['id'],
                                'location_id' => $locationId,
                                'department_id' => $deptIds[array_rand($deptIds)],
                                'size_id' => $sizeId,
                                'quantity' => $outQty,
                                'user_id' => $_SESSION['user_id'] ?? null,
                                'remarks' => 'Demo seed OUT',
                                'seller_id' => null,
                                'price_per_unit' => null
                                ]
                            );
                        }
                    }
                }
            }

            // Add some notifications
            $notificationModel = $this->model('NotificationModel');
            $notificationModel->addNotification($_SESSION['user_id'], 'Welcome to the H&S Inventory Management System!', 'system');
            $notificationModel->addNotification($_SESSION['user_id'], 'Remember to check your stock levels regularly.', 'system');

            $this->addLog('Demo data seeded', 'Generated demo products, inventory, and transactions');
            $this->db->commit();
            $_SESSION['success'] = 'Demo data generated successfully';
        } catch (Exception $ex) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Seeding failed: ' . $ex->getMessage();
        }

        $this->redirect('/');
    }

    public function getStats()
    {
        $this->requireAuth();
        header('Content-Type: application/json');

        $inventoryModel = $this->model('InventoryModel');
        $productModel = $this->model('ProductModel');
        $transactionModel = $this->model('TransactionModel');

        $data = [
            'total_products' => $productModel->count(),
            'total_inventory' => $inventoryModel->getTotalStockQuantity(), // Assuming this method exists

            'todays_transactions' => $transactionModel->getTodaysTransactionCount() // Assuming this method exists
        ];

        echo json_encode($data);
        exit;
    }



    private function getStockByLocation($inventoryModel)
    {
        $locations = $this->db->query("SELECT * FROM locations")->fetchAll();
        $stockData = [];

        foreach ($locations as $location) {
            $stock = $inventoryModel->getStockByLocation($location['id']);
            $totalQuantity = array_sum(array_column($stock, 'quantity'));
            $stockData[] = [
                'location' => $location['name'],
                'total_items' => count($stock),
                'total_quantity' => $totalQuantity
            ];
        }

        return $stockData;
    }
}
