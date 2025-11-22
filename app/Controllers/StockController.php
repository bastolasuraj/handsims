<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\NotificationHelper;
use InvalidArgumentException;
use RuntimeException;

require_once __DIR__ . '/../Helpers/csrf.php';

class StockController extends Controller
{
    public function add()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF Protection
            csrf_check();

            try {
                $productId = (int)($_POST['product_id'] ?? 0);
                $locationId = (int)($_POST['location_id'] ?? 0);
                $sizeId = !empty($_POST['size_id']) ? (int)$_POST['size_id'] : null;
                $quantity = (int)($_POST['quantity'] ?? 0);
                $remarks = $_POST['remarks'] ?? '';
                $sellerId = !empty($_POST['seller_id']) ? (int)$_POST['seller_id'] : null;
                $pricePerUnit = isset($_POST['price_per_unit']) && $_POST['price_per_unit'] !== '' ? $_POST['price_per_unit'] : null;

                $data = [
                    'product_id' => $productId,
                    'location_id' => $locationId,
                    'size_id' => $sizeId,
                    'quantity' => $quantity,
                    'remarks' => $remarks,
                    'seller_id' => $sellerId,
                    'price_per_unit' => $pricePerUnit
                ];

                $errors = $this->validateStockData($data, 'add');
                if (!empty($errors)) {
                    throw new InvalidArgumentException(implode(' ', $errors));
                }

                // Update inventory
                $inventoryModel = $this->model('InventoryModel');
                $inventoryModel->updateStock($productId, $locationId, $sizeId, $quantity, 'add');

                // Add transaction record
                $transactionModel = $this->model('TransactionModel');
                $transactionModel->addTransaction([
                    'transaction_type' => 'IN',
                    'product_id' => $productId,
                    'location_id' => $locationId,
                    'department_id' => null,
                    'size_id' => $sizeId,
                    'quantity' => $quantity,
                    'user_id' => $_SESSION['user_id'],
                    'remarks' => $remarks,
                    'seller_id' => $sellerId,
                    'price_per_unit' => $pricePerUnit
                ]);

                // Add log
                $productModel = $this->model('ProductModel');
                $product = $productModel->getById($productId);
                $locationName = $this->getLocationName($locationId);
                $sizeName = $this->getSizeName($sizeId);

                $logDetails = $_SESSION['username'] . " added " . $quantity . " " . $sizeName . " " .
                             $product['product_type'] . " to " . $locationName . " warehouse";
                $this->addLog("Stock added", $logDetails);

                // Create notification for other users
                $notificationHelper = new NotificationHelper($this->db);
                $notificationHelper->notifyStockAdded(
                    $_SESSION['user_id'],
                    $_SESSION['username'],
                    $product['product_type'],
                    $sizeName,
                    $quantity,
                    $locationName
                );

                // Check inventory levels after adding stock
                $notificationHelper->checkInventoryLevels();

                // Personalized success message using toast template
                $toastTemplateModel = $this->model('ToastTemplateModel');
                $_SESSION['success'] = $toastTemplateModel->formatMessage('stock_add_success', [
                    '%q' => $quantity,
                    '%s' => $sizeName,
                    '%p' => $product['product_type'],
                    '%l' => $locationName
                ]);
            } catch (Exception $e) {
                $_SESSION['error'] = 'Failed to add stock: ' . $e->getMessage();
            }

            $this->redirect('/stock/add');
        }

        // Get data for form
        $data = [
            'products' => $this->model('ProductModel')->getAll(),
            'locations' => $this->getLocations(),
            'sizes' => $this->getSizes(),
            'categories' => $this->getCategories(),
            'sellers' => $this->getSellers()
        ];

        // Pre-load data from query parameters
        $selectedProduct = null;
        $selectedLocation = null;
        $selectedSize = null;

        if (isset($_GET['product_id'])) {
            $productModel = $this->model('ProductModel');
            $selectedProduct = $productModel->getById((int)$_GET['product_id']);
        }

        if (isset($_GET['location_id'])) {
            $locationId = (int)$_GET['location_id'];
            $selectedLocation = array_filter($data['locations'], function ($loc) use ($locationId) {
                return $loc['id'] == $locationId;
            });
            $selectedLocation = !empty($selectedLocation) ? reset($selectedLocation) : null;
        }

        if (isset($_GET['size_id'])) {
            $sizeId = (int)$_GET['size_id'];
            $selectedSize = array_filter($data['sizes'], function ($size) use ($sizeId) {
                return $size['id'] == $sizeId;
            });
            $selectedSize = !empty($selectedSize) ? reset($selectedSize) : null;
        }

        $data['selected_product'] = $selectedProduct;
        $data['selected_location'] = $selectedLocation;
        $data['selected_size'] = $selectedSize;

        $this->view('stock/add', $data);
    }

    public function out()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF Protection
            csrf_check();
            $productId = (int)($_POST['product_id'] ?? 0);
            $locationId = $_POST['location_id'] ?? '';
            $departmentId = (int)($_POST['department_id'] ?? 0);
            $sizeId = !empty($_POST['size_id']) ? $_POST['size_id'] : null;
            $quantity = $_POST['quantity'] ?? 0;
            $remarks = $_POST['remarks'] ?? '';

            $data = [
                'product_id' => $productId,
                'location_id' => $locationId,
                'department_id' => $departmentId,
                'size_id' => $sizeId,
                'quantity' => $quantity,
                'remarks' => $remarks
            ];

            $errors = $this->validateStockData($data, 'out');
            if (!empty($errors)) {
                $_SESSION['error'] = implode(' ', $errors);
                $this->redirect('/stock/out');
                return;
            }

            // Update inventory with proper error handling
            $inventoryModel = $this->model('InventoryModel');
            $result = $inventoryModel->subtractStock($productId, $locationId, $sizeId, $quantity);

            if (!$result['ok']) {
                $errorMsg = 'Insufficient stock at selected location/size. ';
                if (isset($result['available'])) {
                    $errorMsg .= 'Available: ' . $result['available'] . ', Requested: ' . $result['requested'];
                }
                $_SESSION['error'] = $errorMsg;
                $this->redirect('/stock/out');
                return;
            }

            // Add transaction record
            $transactionModel = $this->model('TransactionModel');
            $transactionModel->addTransaction([
                'transaction_type' => 'OUT',
                'product_id' => $productId,
                'location_id' => $locationId,
                'department_id' => $departmentId,
                'size_id' => $sizeId,
                'quantity' => $quantity,
                'user_id' => $_SESSION['user_id'],
                'remarks' => $remarks,
                'seller_id' => null,
                'price_per_unit' => null
            ]);

            // Add log
            $productModel = $this->model('ProductModel');
            $product = $productModel->getById($productId);
            $locationName = $this->getLocationName($locationId);
            $departmentName = $this->getDepartmentName($departmentId);
            $sizeName = $this->getSizeName($sizeId);

            $logDetails = $_SESSION['username'] . " gave " . $quantity . " " . $sizeName . " " .
                         $product['product_type'] . " to " . $departmentName . " from " . $locationName;
            $this->addLog("Stock removed", $logDetails);

            // Create notification for other users
            $notificationHelper = new NotificationHelper($this->db);
            $notificationHelper->notifyStockRemoved(
                $_SESSION['user_id'],
                $_SESSION['username'],
                $product['product_type'],
                $sizeName,
                $quantity,
                $locationName,
                $departmentName
            );

            // Check inventory levels after removing stock (IMPORTANT!)
            $notificationHelper->checkInventoryLevels();

            // Personalized success message using toast template
            $toastTemplateModel = $this->model('ToastTemplateModel');
            $_SESSION['success'] = $toastTemplateModel->formatMessage('stock_out_success', [
                '%q' => $quantity,
                '%s' => $sizeName,
                '%p' => $product['product_type'],
                '%d' => $departmentName,
                '%l' => $locationName
            ]);
            $this->redirect('/stock/out');
        }

        // Get data for form
        $data = [
            'products' => $this->model('ProductModel')->getAll(),
            'locations' => $this->getLocations(),
            'departments' => $this->getDepartments(),
            'sizes' => $this->getSizes(),
            'categories' => $this->getCategories()
        ];

        // Pre-load data from query parameters
        $selectedProduct = null;
        $selectedLocation = null;
        $selectedSize = null;

        if (isset($_GET['product_id'])) {
            $productModel = $this->model('ProductModel');
            $selectedProduct = $productModel->getById((int)$_GET['product_id']);
        }

        if (isset($_GET['location_id'])) {
            $locationId = (int)$_GET['location_id'];
            $selectedLocation = array_filter($data['locations'], function ($loc) use ($locationId) {
                return $loc['id'] == $locationId;
            });
            $selectedLocation = !empty($selectedLocation) ? reset($selectedLocation) : null;
        }

        if (isset($_GET['size_id'])) {
            $sizeId = (int)$_GET['size_id'];
            $selectedSize = array_filter($data['sizes'], function ($size) use ($sizeId) {
                return $size['id'] == $sizeId;
            });
            $selectedSize = !empty($selectedSize) ? reset($selectedSize) : null;
        }

        $data['selected_product'] = $selectedProduct;
        $data['selected_location'] = $selectedLocation;
        $data['selected_size'] = $selectedSize;

        $this->view('stock/out', $data);
    }

    public function inStock()
    {
        $this->requireAuth();

        $inventoryModel = $this->model('InventoryModel');
        $locations = $this->getLocations();

        $selectedLocation = $_GET['location'] ?? null;
        $stockData = $inventoryModel->getStockByLocation($selectedLocation);

        $data = [
            'locations' => $locations,
            'stock' => $stockData,
            'selected_location' => $selectedLocation
        ];

        // Check if it's an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->partial('stock/partials/stock_content', $data);
        } else {
            $this->view('stock/in-stock', $data);
        }
    }

    public function transfer()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF Protection
            csrf_check();
            $productId = (int)($_POST['product_id'] ?? 0);
            $fromLocationId = $_POST['from_location_id'] ?? '';
            $toLocationId = $_POST['to_location_id'] ?? '';
            $sizeId = !empty($_POST['size_id']) ? $_POST['size_id'] : null;
            $quantity = (int)($_POST['quantity'] ?? 0);
            $remarks = $_POST['remarks'] ?? '';

            $data = [
                'product_id' => $productId,
                'location_id' => $fromLocationId,
                'size_id' => $sizeId,
                'quantity' => $quantity
            ];

            $errors = $this->validateStockData($data, 'transfer');
            if ($toLocationId === '' || $toLocationId <= 0) {
                $errors[] = 'Please select a valid destination location.';
            }
            if ($fromLocationId == $toLocationId) {
                $errors[] = 'Source and destination locations must be different.';
            }

            if (!empty($errors)) {
                $_SESSION['error'] = implode(' ', $errors);
                return $this->redirect('/stock/transfer');
            }

            // Atomic transfer with proper transaction handling
            $inventoryModel = $this->model('InventoryModel');
            $transactionModel = $this->model('TransactionModel');
            $productModel = $this->model('ProductModel');

            $db = $this->db;
            $db->beginTransaction();

            try {
                // First check if we have sufficient stock at source
                $result = $inventoryModel->subtractStock($productId, $fromLocationId, $sizeId, $quantity);

                if (!$result['ok']) {
                    throw new RuntimeException('Insufficient stock at source location. Available: ' .
                        ($result['available'] ?? 0) . ', Requested: ' . $quantity);
                }

                // Add to destination
                $inventoryModel->updateStock($productId, $toLocationId, $sizeId, $quantity, 'add');

                // Get names for logging
                $product = $productModel->getById($productId);
                $fromLocationName = $this->getLocationName($fromLocationId);
                $toLocationName = $this->getLocationName($toLocationId);
                $sizeName = $this->getSizeName($sizeId);

                $transferRemark = "transfer\n" . $fromLocationName . " -> " . $toLocationName;
                if (!empty($remarks)) {
                    $transferRemark .= "\n" . $remarks;
                }

                // Record transaction as two entries for traceability
                $transactionModel->addTransaction([
                    'transaction_type' => 'OUT',
                    'product_id' => $productId,
                    'location_id' => $fromLocationId,
                    'department_id' => null,
                    'size_id' => $sizeId,
                    'quantity' => $quantity,
                    'user_id' => $_SESSION['user_id'],
                    'remarks' => $transferRemark,
                    'seller_id' => null,
                    'price_per_unit' => null
                ]);

                $transactionModel->addTransaction([
                    'transaction_type' => 'IN',
                    'product_id' => $productId,
                    'location_id' => $toLocationId,
                    'department_id' => null,
                    'size_id' => $sizeId,
                    'quantity' => $quantity,
                    'user_id' => $_SESSION['user_id'],
                    'remarks' => $transferRemark,
                    'seller_id' => null,
                    'price_per_unit' => null
                ]);

                $db->commit();

                // Log the transfer
                $logDetails = $_SESSION['username'] . " transferred " . $quantity . " " . $sizeName . " " .
                             $product['product_type'] . " from " . $fromLocationName . " to " . $toLocationName;
                $this->addLog("Stock transferred", $logDetails);

                // Create notification for other users
                $notificationHelper = new NotificationHelper($this->db);
                $notificationHelper->notifyStockTransferred(
                    $_SESSION['user_id'],
                    $_SESSION['username'],
                    $product['product_type'],
                    $sizeName,
                    $quantity,
                    $fromLocationName,
                    $toLocationName
                );

                // Check inventory levels after transfer
                $notificationHelper->checkInventoryLevels();

                // Personalized success message using toast template
                $toastTemplateModel = $this->model('ToastTemplateModel');
                $_SESSION['success'] = $toastTemplateModel->formatMessage('stock_transfer_success', [
                    '%q' => $quantity,
                    '%s' => $sizeName,
                    '%p' => $product['product_type'],
                    '%f' => $fromLocationName,
                    '%t' => $toLocationName
                ]);
                return $this->redirect('/stock/transfer');
            } catch (Exception $e) {
                $db->rollBack();
                $_SESSION['error'] = 'Transfer failed: ' . $e->getMessage();
                return $this->redirect('/stock/transfer');
            }
        }

        $data = [
            'products' => $this->model('ProductModel')->getAll(),
            'locations' => $this->getLocations(),
            'sizes' => $this->getSizes(),
            'categories' => $this->getCategories(),
            'sellers' => $this->getSellers()
        ];

        // Pre-load data from query parameters
        $selectedProduct = null;
        $selectedFromLocation = null;
        $selectedSize = null;

        if (isset($_GET['product_id'])) {
            $productModel = $this->model('ProductModel');
            $selectedProduct = $productModel->getById((int)$_GET['product_id']);
        }

        if (isset($_GET['from_location_id'])) {
            $locationId = (int)$_GET['from_location_id'];
            $selectedFromLocation = array_filter($data['locations'], function ($loc) use ($locationId) {
                return $loc['id'] == $locationId;
            });
            $selectedFromLocation = !empty($selectedFromLocation) ? reset($selectedFromLocation) : null;
        }

        if (isset($_GET['size_id'])) {
            $sizeId = (int)$_GET['size_id'];
            $selectedSize = array_filter($data['sizes'], function ($size) use ($sizeId) {
                return $size['id'] == $sizeId;
            });
            $selectedSize = !empty($selectedSize) ? reset($selectedSize) : null;
        }

        $data['selected_product'] = $selectedProduct;
        $data['selected_from_location'] = $selectedFromLocation;
        $data['selected_size'] = $selectedSize;

        $this->view('stock/transfer', $data);
    }

    private function getSellers()
    {
        $sql = "SELECT * FROM sellers ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
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

    private function getSizes()
    {
        $sql = "SELECT * FROM product_sizes ORDER BY id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getCategories()
    {
        $sql = "SELECT * FROM categories ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getLocationName($id)
    {
        $sql = "SELECT name FROM locations WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ? $result['name'] : 'Unknown';
    }

    private function getDepartmentName($id)
    {
        $sql = "SELECT name FROM departments WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ? $result['name'] : 'Unknown';
    }

    private function getSizeName($id)
    {
        if (!$id) {
            return '';
        }
        $sql = "SELECT size FROM product_sizes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ? $result['size'] : '';
    }

    public function getInventoryJson()
    {
        $this->requireAuth();
        $inventoryModel = $this->model('InventoryModel');
        $stockData = $inventoryModel->getStockByLocation();

        header('Content-Type: application/json');
        echo json_encode($stockData);
        exit;
    }

    public function getProductSizes()
    {
        $this->requireAuth();

        $productId = $_GET['product_id'] ?? null;

        if (!$productId) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Product ID required']);
            exit;
        }

        $productModel = $this->model('ProductModel');
        $sizes = $productModel->getAvailableSizes($productId);

        header('Content-Type: application/json');
        echo json_encode($sizes);
        exit;
    }

    public function getAvailableSizesInStock()
    {
        $this->requireAuth();

        $productId = $_GET['product_id'] ?? null;
        $locationId = $_GET['location_id'] ?? null;

        if (!$productId || !$locationId) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Product ID and Location ID required']);
            exit;
        }

        $productModel = $this->model('ProductModel');
        $sizes = $productModel->getAvailableSizesInStock($productId, $locationId);

        header('Content-Type: application/json');
        echo json_encode($sizes);
        exit;
    }

    public function bulkAdd()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/stock/add');
            return;
        }

        if (!isset($_FILES['bulk_file']) || $_FILES['bulk_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'File upload error. Please try again.';
            $this->redirect('/stock/add');
            return;
        }

        $file = $_FILES['bulk_file']['tmp_name'];
        $productModel = $this->model('ProductModel');
        $inventoryModel = $this->model('InventoryModel');
        $transactionModel = $this->model('TransactionModel');

        // Pre-load locations and sellers for faster lookup
        $allLocations = $this->getLocations();
        $locationMap = [];
        foreach ($allLocations as $location) {
            $locationMap[strtoupper($location['name'])] = $location['id'];
        }

        $handle = fopen($file, "r");
        if ($handle === false) {
            $_SESSION['error'] = 'Could not open the file.';
            $this->redirect('/stock/add');
            return;
        }

        $header = fgetcsv($handle, 1000, ",");
        // Expected header: part_number,location_name,size_id,quantity,remarks,seller_id,price_per_unit

        $rowCount = 0;
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        $this->db->beginTransaction();

        try {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $rowCount++;
                $rowData = array_combine($header, $data);

                // Basic validation
                if (empty($rowData['part_number']) || empty($rowData['location_name']) || empty($rowData['quantity'])) {
                    $errorCount++;
                    $errors[] = "Row {$rowCount}: Part number, location name, and quantity are required.";
                    continue;
                }

                // Lookups
                $product = $productModel->getByPartNumber($rowData['part_number']);
                if (!$product) {
                    $errorCount++;
                    $errors[] = "Row {$rowCount}: Product with part number '{$rowData['part_number']}' not found.";
                    continue;
                }
                $productId = $product['id'];

                $locationName = strtoupper(trim($rowData['location_name']));
                if (!isset($locationMap[$locationName])) {
                    $errorCount++;
                    $errors[] = "Row {$rowCount}: Location '{$rowData['location_name']}' not found.";
                    continue;
                }
                $locationId = $locationMap[$locationName];

                $sizeId = !empty($rowData['size_id']) ? (int)$rowData['size_id'] : null;
                $quantity = (int)$rowData['quantity'];
                $remarks = $rowData['remarks'] ?? '';
                $sellerId = !empty($rowData['seller_id']) ? (int)$rowData['seller_id'] : null;
                $pricePerUnit = isset($rowData['price_per_unit']) && $rowData['price_per_unit'] !== '' ? $rowData['price_per_unit'] : null;

                // Update inventory
                $inventoryModel->updateStock($productId, $locationId, $sizeId, $quantity, 'add');

                // Add transaction record
                $transactionModel->addTransaction([
                    'transaction_type' => 'IN',
                    'product_id' => $productId,
                    'location_id' => $locationId,
                    'department_id' => null,
                    'size_id' => $sizeId,
                    'quantity' => $quantity,
                    'user_id' => $_SESSION['user_id'],
                    'remarks' => "Bulk add: " . $remarks,
                    'seller_id' => $sellerId,
                    'price_per_unit' => $pricePerUnit
                ]);

                $successCount++;
            }

            if ($errorCount > 0) {
                throw new Exception("Errors occurred during bulk import.");
            }

            $this->db->commit();
            $_SESSION['success'] = "Bulk stock import finished. {$successCount} records imported successfully.";
        } catch (Exception $e) {
            $this->db->rollBack();
            $message = "Bulk import failed. " . $e->getMessage();
            if ($errorCount > 0) {
                $_SESSION['error'] = $message . " {$errorCount} rows failed. Errors: " . implode('; ', $errors);
            } else {
                $_SESSION['error'] = $message;
            }
        } finally {
            fclose($handle);
        }

        $this->redirect('/stock/in-stock');
    }

    public function checkStock()
    {
        $this->requireAuth();

        header('Content-Type: application/json');

        $productId = (int)($_GET['product_id'] ?? 0);
        $locationId = (int)($_GET['location_id'] ?? 0);
        $sizeId = !empty($_GET['size_id']) ? (int)$_GET['size_id'] : null;

        if ($productId <= 0 || $locationId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product or location ID.']);
            exit;
        }

        $inventoryModel = $this->model('InventoryModel');
        $available = $inventoryModel->getAvailableStock($productId, $locationId, $sizeId);

        echo json_encode(['success' => true, 'available' => $available]);
        exit;
    }

    private function validateStockData($data, $type = 'add')
    {
        $errors = [];

        if (empty($data['product_id']) || !is_numeric($data['product_id']) || (int)$data['product_id'] <= 0) {
            $errors[] = "A valid product must be selected.";
        }

        if (empty($data['location_id']) || !is_numeric($data['location_id']) || (int)$data['location_id'] <= 0) {
            $errors[] = "A valid location must be selected.";
        }

        if (empty($data['quantity']) || !is_numeric($data['quantity']) || (int)$data['quantity'] <= 0) {
            $errors[] = "Quantity must be a positive number.";
        }

        if ($type === 'out') {
            if (empty($data['department_id']) || !is_numeric($data['department_id']) || (int)$data['department_id'] <= 0) {
                $errors[] = "A valid department must be selected.";
            }
        }

        if ($type === 'add') {
            if (isset($data['price_per_unit']) && !empty($data['price_per_unit']) && !is_numeric($data['price_per_unit'])) {
                $errors[] = "Price per unit must be a valid number.";
            }
        }

        // Validate remarks length
        if (isset($data['remarks']) && strlen($data['remarks']) > 1000) {
            $errors[] = "Remarks cannot exceed 1000 characters.";
        }

        // Validate size_id against product's available sizes
        if (isset($data['product_id']) && !empty($data['product_id']) && isset($data['size_id']) && !empty($data['size_id'])) {
            $productModel = $this->model('ProductModel');
            $product = $productModel->getById($data['product_id']);

            if ($product && !empty($product['available_sizes'])) {
                $availableSizeIds = array_map('trim', explode(',', $product['available_sizes']));

                if (!in_array((string)$data['size_id'], $availableSizeIds, true)) {
                    $errors[] = "Invalid size selected for this product. Please select from the available sizes.";
                }
            }
        }

        return $errors;
    }
}
