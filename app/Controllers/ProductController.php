<?php

namespace App\Controllers;

use App\Core\Controller;

require_once __DIR__ . '/../Helpers/csrf.php';

class ProductController extends Controller
{
    public function index()
    {
        $this->requireAuth();

        $productModel = $this->model('ProductModel');
        $products = $productModel->getAllWithCategory();

        $data = [
            'products' => $products
        ];

        $this->view('products/index', $data);
    }

    public function add()
    {
        $this->requireAuth();

        $productModel = $this->model('ProductModel');
        $mode = 'add';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF Protection
            csrf_check();

            $availableSizes = '';
            if (isset($_POST['sizes']) && is_array($_POST['sizes'])) {
                $availableSizes = implode(',', $_POST['sizes']);
            }

            $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;

            $data = [
                'part_number' => trim($_POST['part_number'] ?? ''),
                'type_id' => $_POST['type_id'] ?? null, // Changed from product_type
                'category_id' => $categoryId,
                'description' => trim($_POST['description'] ?? ''),
                'low_stock_threshold' => isset($_POST['low_stock_threshold']) && $_POST['low_stock_threshold'] !== '' ? (int)$_POST['low_stock_threshold'] : 5,
                'qr_code' => $this->generateQRCode($_POST['part_number']),
                'available_sizes' => $availableSizes
            ];

            $errors = $this->validateProductData($data);
            if (!empty($errors)) {
                $data['error'] = implode(' ', $errors);
                $data['categories'] = $this->getCategories();
                $data['sizes'] = $this->getSizes();
                $data['types'] = $this->getTypes();
                $data['products'] = $productModel->getAllWithCategory();
                $data['mode'] = $mode;
                $this->view('products/add', $data);
                return;
            }

            // Check if part number already exists
            $existingProduct = $productModel->getByPartNumber($data['part_number']);
            if ($existingProduct && (!isset($_POST['update_existing']) || $_POST['update_existing'] !== '1')) {
                $data['error'] = 'Part number already exists. Check the box below to update the existing product.';
                $data['existing_product'] = $existingProduct;
                $data['categories'] = $this->getCategories();
                $data['sizes'] = $this->getSizes();
                $data['types'] = $this->getTypes(); // Changed from product_types
                $data['products'] = $productModel->getAllWithCategory(); // Add products list
                $this->view('products/add', $data);
                return;
            }

            if ($existingProduct && $_POST['update_existing'] === '1') {
                // Update existing product
                if ($productModel->update($existingProduct['id'], $data)) {
                    $this->addLog("Product updated", "Part Number: " . $data['part_number'] . ", Type ID: " . $data['type_id']); // Updated log
                    $_SESSION['success'] = 'Product updated successfully';
                    $this->redirect('/products/detail?id=' . $existingProduct['id']);
                } else {
                    $data['error'] = 'Failed to update product';
                }
            } else {
                // Create new product
                if ($productModel->create($data)) {
                    $this->addLog("Product added", "Part Number: " . $data['part_number'] . ", Type ID: " . $data['type_id']); // Updated log
                    $_SESSION['success'] = 'Product added successfully';
                    $this->redirect('/products');
                } else {
                    $data['error'] = 'Failed to add product';
                }
            }

            $data['categories'] = $this->getCategories();
            $data['sizes'] = $this->getSizes();
            $data['types'] = $this->getTypes(); // Changed from product_types
            $data['products'] = $productModel->getAllWithCategory();
            $data['mode'] = $mode; // Explicitly pass mode
            $this->view('products/add', $data);
        } else { // GET request
            $prefillData = null;
            if (isset($_GET['part_number']) && !empty($_GET['part_number'])) {
                $prefillData = $productModel->getByPartNumber($_GET['part_number']);
            }

            $data = [
                'categories' => $this->getCategories(),
                'sizes' => $this->getSizes(),
                'types' => $this->getTypes(), // Changed from product_types
                'products' => $productModel->getAllWithCategory(),
                'prefill' => $prefillData,
                'mode' => $mode // Explicitly pass mode
            ];
            $this->view('products/add', $data);
        }
    }

    public function edit()
    {
        $this->requireAuth();

        $id = $_GET['id'] ?? 0;
        if (!$id || $id <= 0) {
            $_SESSION['error'] = 'Invalid product ID';
            $this->redirect('/products');
            return;
        }

        $productModel = $this->model('ProductModel');
        $mode = 'edit'; // Explicitly set mode

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF Protection
            csrf_check();

            // Verify we have a valid product_id for edit mode
            $postId = (int)($_POST['product_id'] ?? 0);
            if ($postId !== (int)$id) {
                $_SESSION['error'] = 'Product ID mismatch';
                $this->redirect('/products');
                return;
            }
            // Handle category_id - ensure it's null if empty or invalid
            $categoryId = $_POST['category_id'] ?? null;
            if (empty($categoryId) || $categoryId === '' || $categoryId === '0') {
                $categoryId = null;
            }

            // Handle available sizes
            $availableSizes = '';
            if (isset($_POST['sizes']) && is_array($_POST['sizes'])) {
                $availableSizes = implode(',', $_POST['sizes']);
            }

            $data = [
                'id' => $id, // For validation
                'part_number' => trim($_POST['part_number'] ?? ''),
                'type_id' => $_POST['type_id'] ?? null, // Changed from product_type
                'category_id' => $categoryId,
                'description' => trim($_POST['description'] ?? ''),
                'low_stock_threshold' => isset($_POST['low_stock_threshold']) && $_POST['low_stock_threshold'] !== '' ? (int)$_POST['low_stock_threshold'] : 5,
                'qr_code' => $this->generateQRCode($_POST['part_number']),
                'available_sizes' => $availableSizes
            ];

            $errors = $this->validateProductData($data, true); // true for update
            if (!empty($errors)) {
                $data['error'] = implode(' ', $errors);
                $data['product'] = $productModel->getProductDetails($id);
                $data['categories'] = $this->getCategories();
                $data['sizes'] = $this->getSizes();
                $data['types'] = $this->getTypes();
                $data['mode'] = $mode;
                $this->view('products/edit', $data);
                return;
            }

            if ($productModel->update($id, $data)) {
                $this->addLog("Product updated", "Part Number: " . $data['part_number'] . ", Type ID: " . $data['type_id']); // Updated log
                $_SESSION['success'] = 'Product updated successfully';
                $this->redirect('/products');
            } else {
                $data['error'] = 'Failed to update product';
                // When update fails, we need to re-fetch product data for the form
                $data['product'] = $productModel->getProductDetails($id); // Use getProductDetails
                $data['categories'] = $this->getCategories();
                $data['sizes'] = $this->getSizes();
                $data['types'] = $this->getTypes(); // Changed from product_types
                $data['mode'] = $mode; // Pass mode
                $this->view('products/edit', $data);
            }
        } else {
            $product = $productModel->getProductDetails($id); // Use getProductDetails

            if (!$product) {
                $this->redirect('/products');
            }

            $data = [
                'product' => $product,
                'categories' => $this->getCategories(),
                'sizes' => $this->getSizes(),
                'types' => $this->getTypes(), // Changed from product_types
                'mode' => $mode // Pass mode
            ];
            $this->view('products/edit', $data);
        }
    }

    public function detail()
    {
        $this->requireAuth();

        $id = $_GET['id'] ?? 0;
        $productModel = $this->model('ProductModel');
        $transactionModel = $this->model('TransactionModel');

        $product = $productModel->getProductDetails($id);

        if (!$product) {
            $this->redirect('/products');
        }

        $data = [
            'product' => $product,
            'inventory' => $productModel->getProductInventory($id),
            'transactions' => $transactionModel->getProductTransactions($id)
        ];

        $this->view('products/detail', $data);
    }

    public function bulkAdd()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/products');
            return;
        }

        if (!isset($_FILES['bulk_file']) || $_FILES['bulk_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'File upload error. Please try again.';
            $this->redirect('/products');
            return;
        }

        $file = $_FILES['bulk_file']['tmp_name'];
        $productModel = $this->model('ProductModel');

        $allCategories = $this->getCategories();
        $categoryMap = [];
        foreach ($allCategories as $category) {
            $categoryMap[strtoupper($category['name'])] = $category['id'];
        }

        $handle = fopen($file, "r");
        if ($handle === false) {
            $_SESSION['error'] = 'Could not open the file.';
            $this->redirect('/products');
            return;
        }

        $header = fgetcsv($handle, 1000, ",");
        // Update header expectation: part_number,type_id,category_name,description,low_stock_threshold,available_sizes

        $rowCount = 0;
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $rowCount++;
            $rowData = array_combine($header, $data);

            // Basic validation
            if (empty($rowData['part_number'])) {
                $errorCount++;
                $errors[] = "Row {$rowCount}: Part number is required.";
                continue;
            }
            if (empty($rowData['type_id'])) {
                $errorCount++;
                $errors[] = "Row {$rowCount}: Type ID is required.";
                continue;
            }

            // Category lookup
            $categoryId = null;
            if (!empty($rowData['category_name'])) {
                $categoryName = strtoupper(trim($rowData['category_name']));
                if (isset($categoryMap[$categoryName])) {
                    $categoryId = $categoryMap[$categoryName];
                } else {
                    // For now, we'll just ignore it.
                }
            }

            $productData = [
                'part_number' => $rowData['part_number'],
                'type_id' => $rowData['type_id'],
                'category_id' => $categoryId,
                'description' => $rowData['description'] ?? '',
                'low_stock_threshold' => !empty($rowData['low_stock_threshold']) ? (int)$rowData['low_stock_threshold'] : 5,
                'qr_code' => $this->generateQRCode($rowData['part_number']),
                'available_sizes' => $rowData['available_sizes'] ?? ''
            ];

            $validationErrors = $this->validateProductData($productData);
            if (!empty($validationErrors)) {
                $errorCount++;
                $errors[] = "Row {$rowCount} (Part No: {$productData['part_number']}): " . implode(' ', $validationErrors);
                continue;
            }

            $existingProduct = $productModel->getByPartNumber($productData['part_number']);

            if ($existingProduct) {
                // Update existing product
                if ($productModel->update($existingProduct['id'], $productData)) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Row {$rowCount}: Failed to update product with part number " . $productData['part_number'];
                }
            } else {
                // Create new product
                if ($productModel->create($productData)) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Row {$rowCount}: Failed to create product with part number " . $productData['part_number'];
                }
            }
        }

        fclose($handle);

        $message = "Bulk product import finished. {$successCount} products imported/updated successfully.";
        if ($errorCount > 0) {
            $_SESSION['error'] = $message . " {$errorCount} rows failed. Errors: " . implode('; ', $errors);
        } else {
            $_SESSION['success'] = $message;
        }

        $this->redirect('/products');
    }



    public function delete()
    {
        $this->requireAuth();

        // Only local users can delete products (not LDAP users)
        if (!isset($_SESSION['auth_type']) || $_SESSION['auth_type'] !== 'local') {
            $_SESSION['error'] = 'Unauthorized: Only local users can delete products';
            $this->redirect('/products');
            return;
        }

        // CSRF check for delete operations
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
        }

        $id = $_GET['id'] ?? 0;
        $productModel = $this->model('ProductModel');

        $product = $productModel->getById($id);
        if ($product) {
            // Check if product has any stock
            $stmt = $this->db->prepare("SELECT SUM(quantity) as total FROM inventory WHERE product_id = ?");
            $stmt->execute([$id]);
            $stock = $stmt->fetch();
            $totalStock = $stock['total'] ?? 0;

            if ($totalStock > 0) {
                $_SESSION['error'] = 'Cannot delete product with existing stock. Current stock: ' . $totalStock . ' units. Please remove all stock first.';
                $this->redirect('/products');
                return;
            }

            if ($productModel->delete($id)) {
                $this->addLog("Product deleted", "Part Number: " . $product['part_number']);
                $_SESSION['success'] = 'Product deleted successfully';
            } else {
                $_SESSION['error'] = 'Failed to delete product';
            }
        }

        $this->redirect('/products');
    }

    public function searchByBarcode()
    {
        $this->requireAuth();
        header('Content-Type: application/json');

        $term = $_GET['term'] ?? '';

        if (empty($term)) {
            echo json_encode(['success' => false, 'message' => 'Search term is required.']);
            exit;
        }

        $productModel = $this->model('ProductModel');
        $products = $productModel->searchByPartNumber($term); // Assuming a method like this exists or will be created

        echo json_encode($products);
        exit;
    }


    private function getCategories()
    {
        $sql = "SELECT * FROM categories ORDER BY name";
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

    private function getTypes()
    {
        // Get all types with their category information
        $sql = "SELECT t.*, c.name as category_name 
                FROM types t
                LEFT JOIN categories c ON t.category_id = c.id
                ORDER BY t.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function generateQRCode($partNumber)
    {
        // Generate a unique QR code identifier
        // In production, you might want to use a QR code library
        return 'QR_' . $partNumber . '_' . time();
    }

    // Validation helper for products
    private function validateProductData($data, $isUpdate = false)
    {
        $errors = [];

        // Validate part_number
        if (empty($data['part_number'])) {
            $errors[] = "Part number is required.";
        } elseif (strlen($data['part_number']) > 50) {
            $errors[] = "Part number cannot exceed 50 characters.";
        }

        // Validate type_id
        if (empty($data['type_id']) || !is_numeric($data['type_id']) || (int)$data['type_id'] <= 0) {
            $errors[] = "Product Type is required.";
        }

        // Validate category_id
        if (empty($data['category_id']) || !is_numeric($data['category_id']) || (int)$data['category_id'] <= 0) {
            $errors[] = "Category is required.";
        }

        // Validate description
        if (isset($data['description']) && strlen($data['description']) > 1000) { // Assuming a reasonable text limit
            $errors[] = "Description cannot exceed 1000 characters.";
        }

        // Validate low_stock_threshold
        if (!isset($data['low_stock_threshold']) || $data['low_stock_threshold'] === '' || !is_numeric($data['low_stock_threshold']) || (int)$data['low_stock_threshold'] < 0) {
            $errors[] = "Low stock threshold is required and must be a non-negative number.";
        }
        
        // Validate available_sizes
        if (empty($data['available_sizes'])) {
            $errors[] = "At least one size must be selected.";
        }

        // For update operations, validate ID
        if ($isUpdate) {
            if (!isset($data['id']) || !is_numeric($data['id']) || (int)$data['id'] <= 0) {
                $errors[] = "Invalid product ID for update.";
            }
        }

        return $errors;
    }
}
