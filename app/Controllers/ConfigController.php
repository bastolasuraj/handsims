<?php

namespace App\Controllers;

use App\Core\Controller;

require_once __DIR__ . '/../Helpers/csrf.php';

class ConfigController extends Controller
{

    // Helper method to validate CSRF for POST requests
    private function validateCSRF($is_ajax = false)
    {
        if (!$is_ajax) {
            csrf_check();
        } else {
            if (!csrf_verify($_POST['csrf'] ?? '')) {
                header('Content-Type: application/json');
                http_response_code(419);
                echo json_encode(['success' => false, 'message' => 'CSRF validation failed']);
                exit;
            }
        }
    }

    public function index()
    {
        $this->requireAuth();

        $typeModel = $this->model('TypeModel');

        // Get counts for dashboard
        $categoryCount = $this->getCategoryCount();
        $sizeCount = $this->getSizeCount();
        $locationCount = $this->getLocationCount();
        $departmentCount = $this->getDepartmentCount();
        $sellerCount = $this->getSellerCount();
        $typeCount = $typeModel->getCount();

        $data = [
            'category_count' => $categoryCount,
            'size_count' => $sizeCount,
            'location_count' => $locationCount,
            'department_count' => $departmentCount,
            'seller_count' => $sellerCount,
            'type_count' => $typeCount
        ];

        $this->view('config/index', $data);
    }

    // ==================== CATEGORIES ====================

    public function categories()
    {
        $this->requireAuth();

        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF($is_ajax);
            $action = $_POST['action'] ?? '';
            $response = ['success' => false, 'message' => ''];

            if ($action === 'add') {
                $data = [
                    'name' => trim($_POST['name'] ?? ''),
                    'description' => trim($_POST['description'] ?? null)
                ];

                $errors = $this->validateCategoryData($data);
                if (!empty($errors)) {
                    $response['message'] = implode(' ', $errors);
                } elseif ($this->addCategory($data)) {
                    $this->addLog("Category added", "Name: " . $data['name']);
                    $response['success'] = true;
                    $response['message'] = 'Category added successfully';
                    $response['new_item'] = $this->getCategoryById($this->db->lastInsertId());
                } else {
                    $response['message'] = 'Failed to add category';
                }
            } elseif ($action === 'edit') {
                $id = $_POST['id'] ?? 0;
                $data = [
                    'id' => $id, // Pass ID for validation
                    'name' => trim($_POST['name'] ?? ''),
                    'description' => trim($_POST['description'] ?? null)
                ];

                $errors = $this->validateCategoryData($data, true); // true for update
                if (!empty($errors)) {
                    $response['message'] = implode(' ', $errors);
                } elseif ($this->updateCategory($id, $data)) {
                    $this->addLog("Category updated", "ID: $id, Name: " . $data['name']);
                    $response['success'] = true;
                    $response['message'] = 'Category updated successfully';
                    $response['updated_item'] = $this->getCategoryById($id);
                } else {
                    $response['message'] = 'Failed to update category';
                }
            } elseif ($action === 'delete') {
                $id = (int)($_POST['id'] ?? 0); // Cast to int for validation

                $errors = $this->validateCategoryData(['id' => $id], true); // Validate ID for delete
                if (!empty($errors)) {
                    $response['message'] = implode(' ', $errors);
                } else {
                    $deleteResult = $this->deleteCategory($id);
                    if ($deleteResult === true) {
                        $this->addLog("Category deleted", "ID: $id");
                        $response['success'] = true;
                        $response['message'] = 'Category deleted successfully';
                        $response['deleted_id'] = $id;
                    } else {
                        $response['message'] = $deleteResult ?: 'Failed to delete category. It may be in use by products.';
                    }
                }
            }

            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                // For non-AJAX requests, redirect as before
                if ($response['success']) {
                    $_SESSION['success'] = $response['message'];
                } else {
                    $_SESSION['error'] = $response['message'];
                }
                $this->redirect('/master-data/categories');
            }
        }

        $edit_id = $_GET['edit_id'] ?? null;
        $editing_category = null;
        if ($edit_id) {
            $editing_category = $this->getCategoryById($edit_id);
        }

        $data = [
            'categories' => $this->getCategories(),
            'editing_category' => $editing_category
        ];

        $this->view('config/categories', $data);
    }

    // ==================== SIZES ====================

    public function sizes()
    {
        $this->requireAuth();

        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF($is_ajax);
            $action = $_POST['action'] ?? '';
            $response = ['success' => false, 'message' => ''];

            if ($action === 'add') {
                $size = trim($_POST['size'] ?? '');

                $errors = $this->validateSizeData($size);
                if (!empty($errors)) {
                    $response['message'] = implode(' ', $errors);
                } elseif ($this->addSize($size)) {
                    $this->addLog("Size added", "Size: $size");
                    $response['success'] = true;
                    $response['message'] = 'Size added successfully';
                    $response['new_item'] = $this->getSizeById($this->db->lastInsertId());
                } else {
                    $response['message'] = 'Failed to add size';
                }
            } elseif ($action === 'edit') {
                $id = (int)($_POST['id'] ?? 0);
                $size = trim($_POST['size'] ?? '');

                $errors = $this->validateSizeData($size, true, $id);
                if (!empty($errors)) {
                    $response['message'] = implode(' ', $errors);
                } elseif ($this->updateSize($id, $size)) {
                    $this->addLog("Size updated", "ID: $id, Size: $size");
                    $response['success'] = true;
                    $response['message'] = 'Size updated successfully';
                    $response['updated_item'] = $this->getSizeById($id);
                } else {
                    $response['message'] = 'Failed to update size';
                }
            } elseif ($action === 'delete') {
                $id = (int)($_POST['id'] ?? 0);

                $errors = $this->validateSizeData(null, true, $id); // Validate ID for delete
                if (!empty($errors)) {
                    $response['message'] = implode(' ', $errors);
                } else {
                    $deleteResult = $this->deleteSize($id);
                    if ($deleteResult === true) {
                        $this->addLog("Size deleted", "ID: $id");
                        $response['success'] = true;
                        $response['message'] = 'Size deleted successfully';
                        $response['deleted_id'] = $id;
                    } else {
                        $response['message'] = $deleteResult ?: 'Failed to delete size. It may be in use in inventory or transactions.';
                    }
                }
            }

            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                // For non-AJAX requests, redirect as before
                if ($response['success']) {
                    $_SESSION['success'] = $response['message'];
                } else {
                    $_SESSION['error'] = $response['message'];
                }
                $this->redirect('/master-data/sizes');
            }
        }

        $edit_id = $_GET['edit_id'] ?? null;
        $editing_size = null;
        if ($edit_id) {
            $editing_size = $this->getSizeById($edit_id);
        }

        $data = [
            'sizes' => $this->getSizes(),
            'editing_size' => $editing_size
        ];

        $this->view('config/sizes', $data);
    }

    // ==================== LOCATIONS ====================

    public function locations()
    {
        $this->requireAuth();

        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF($is_ajax);
            $action = $_POST['action'] ?? '';
            $response = ['success' => false, 'message' => ''];

            if ($action === 'add') {
                $data = [
                    'name' => trim($_POST['name'] ?? ''),
                    'description' => trim($_POST['description'] ?? null)
                ];

                $errors = $this->validateLocationData($data);
                if (!empty($errors)) {
                    $response['message'] = implode(' ', $errors);
                } elseif ($this->addLocation($data)) {
                    $this->addLog("Location added", "Name: " . $data['name']);
                    $response['success'] = true;
                    $response['message'] = 'Location added successfully';
                    $response['new_item'] = $this->getLocationById($this->db->lastInsertId());
                } else {
                    $response['message'] = 'Failed to add location';
                }
            } elseif ($action === 'edit') {
                $id = $_POST['id'] ?? 0;
                $data = [
                    'id' => $id, // Pass ID for validation
                    'name' => trim($_POST['name'] ?? ''),
                    'description' => trim($_POST['description'] ?? null)
                ];

                $errors = $this->validateLocationData($data, true); // true for update
                if (!empty($errors)) {
                    $response['message'] = implode(' ', $errors);
                } elseif ($this->updateLocation($id, $data)) {
                    $this->addLog("Location updated", "ID: $id, Name: " . $data['name']);
                    $response['success'] = true;
                    $response['message'] = 'Location updated successfully';
                    $response['updated_item'] = $this->getLocationById($id);
                } else {
                    $response['message'] = 'Failed to update location';
                }
            } elseif ($action === 'delete') {
                $id = (int)($_POST['id'] ?? 0);

                $errors = $this->validateLocationData(['id' => $id], true); // Validate ID for delete
                if (!empty($errors)) {
                    $response['message'] = implode(' ', $errors);
                } else {
                    $deleteResult = $this->deleteLocation($id);
                    if ($deleteResult === true) {
                        $this->addLog("Location deleted", "ID: $id");
                        $response['success'] = true;
                        $response['message'] = 'Location deleted successfully';
                        $response['deleted_id'] = $id;
                    } else {
                        $response['message'] = $deleteResult ?: 'Failed to delete location. It may have inventory or transactions.';
                    }
                }
            }

            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                // For non-AJAX requests, redirect as before
                if ($response['success']) {
                    $_SESSION['success'] = $response['message'];
                } else {
                    $_SESSION['error'] = $response['message'];
                }
                $this->redirect('/master-data/locations');
            }
        }

        $edit_id = $_GET['edit_id'] ?? null;
        $editing_location = null;
        if ($edit_id) {
            $editing_location = $this->getLocationById($edit_id);
        }

        $data = [
            'locations' => $this->getLocations(),
            'editing_location' => $editing_location
        ];

        $this->view('config/locations', $data);
    }

    // ==================== DEPARTMENTS ====================

    public function departments()
    {
        $this->requireAuth();

        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF($is_ajax);
            $action = $_POST['action'] ?? '';
            $response = ['success' => false, 'message' => ''];

            if ($action === 'add') {
                $data = [
                    'name' => trim($_POST['name'] ?? ''),
                    'description' => trim($_POST['description'] ?? null)
                ];

                $errors = $this->validateDepartmentData($data);
                if (!empty($errors)) {
                    $response['message'] = implode(' ', $errors);
                } elseif ($this->addDepartment($data)) {
                    $this->addLog("Department added", "Name: " . $data['name']);
                    $response['success'] = true;
                    $response['message'] = 'Department added successfully';
                    $response['new_item'] = $this->getDepartmentById($this->db->lastInsertId());
                } else {
                    $response['message'] = 'Failed to add department';
                }
            } elseif ($action === 'edit') {
                $id = $_POST['id'] ?? 0;
                $data = [
                    'id' => $id, // Pass ID for validation
                    'name' => trim($_POST['name'] ?? ''),
                    'description' => trim($_POST['description'] ?? null)
                ];

                $errors = $this->validateDepartmentData($data, true); // true for update
                if (!empty($errors)) {
                    $response['message'] = implode(' ', $errors);
                } elseif ($this->updateDepartment($id, $data)) {
                    $this->addLog("Department updated", "ID: $id, Name: " . $data['name']);
                    $response['success'] = true;
                    $response['message'] = 'Department updated successfully';
                    $response['updated_item'] = $this->getDepartmentById($id);
                } else {
                    $response['message'] = 'Failed to update department';
                }
            } elseif ($action === 'delete') {
                $id = (int)($_POST['id'] ?? 0);

                $errors = $this->validateDepartmentData(['id' => $id], true); // Validate ID for delete
                if (!empty($errors)) {
                    $response['message'] = implode(' ', $errors);
                } else {
                    $deleteResult = $this->deleteDepartment($id);
                    if ($deleteResult === true) {
                        $this->addLog("Department deleted", "ID: $id");
                        $response['success'] = true;
                        $response['message'] = 'Department deleted successfully';
                        $response['deleted_id'] = $id;
                    } else {
                        $response['message'] = $deleteResult ?: 'Failed to delete department. It may have associated transactions.';
                    }
                }
            }

            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                // For non-AJAX requests, redirect as before
                if ($response['success']) {
                    $_SESSION['success'] = $response['message'];
                } else {
                    $_SESSION['error'] = $response['message'];
                }
                $this->redirect('/master-data/departments');
            }
        }

        $edit_id = $_GET['edit_id'] ?? null;
        $editing_department = null;
        if ($edit_id) {
            $editing_department = $this->getDepartmentById($edit_id);
        }

        $data = [
            'departments' => $this->getDepartments(),
            'editing_department' => $editing_department
        ];

        $this->view('config/departments', $data);
    }

    // ==================== PRIVATE HELPER METHODS ====================

    private function getCategories()
    {
        $sql = "SELECT * FROM categories ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getCategoryById($id)
    {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    private function addCategory($data)
    {
        $sql = "INSERT INTO categories (name, description) VALUES (:name, :description)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    private function updateCategory($id, $data)
    {
        $sql = "UPDATE categories SET name = :name, description = :description WHERE id = :id";
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    private function deleteCategory($id)
    {
        // First check if this category is being used by any products
        $checkSql = "SELECT COUNT(*) as count FROM products WHERE category_id = :id";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute(['id' => $id]);
        $result = $checkStmt->fetch();

        if ($result['count'] > 0) {
            return false; // Category is in use, cannot delete
        }

        // If not in use, proceed with deletion
        $sql = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    private function getSizes()
    {
        $sql = "SELECT * FROM product_sizes ORDER BY id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getSizeById($id)
    {
        $sql = "SELECT * FROM product_sizes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    private function addSize($size)
    {
        $sql = "INSERT INTO product_sizes (size) VALUES (:size)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['size' => $size]);
    }

    private function updateSize($id, $size)
    {
        $sql = "UPDATE product_sizes SET size = :size WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id, 'size' => $size]);
    }

    private function deleteSize($id)
    {
        // Check if this size is being used in inventory
        $checkInventory = "SELECT COUNT(*) as count FROM inventory WHERE size_id = :id";
        $checkStmt = $this->db->prepare($checkInventory);
        $checkStmt->execute(['id' => $id]);
        $result = $checkStmt->fetch();

        if ($result['count'] > 0) {
            return false; // Size is in use in inventory
        }

        // Check if this size is being used in stock transactions
        $checkTransactions = "SELECT COUNT(*) as count FROM stock_transactions WHERE size_id = :id";
        $checkStmt = $this->db->prepare($checkTransactions);
        $checkStmt->execute(['id' => $id]);
        $result = $checkStmt->fetch();

        if ($result['count'] > 0) {
            return false; // Size is in use in transactions
        }

        // If not in use, proceed with deletion
        $sql = "DELETE FROM product_sizes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    private function getLocations()
    {
        $sql = "SELECT * FROM locations ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function addLocation($data)
    {
        $sql = "INSERT INTO locations (name, description) VALUES (:name, :description)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    private function updateLocation($id, $data)
    {
        $sql = "UPDATE locations SET name = :name, description = :description WHERE id = :id";
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    private function deleteLocation($id)
    {
        // Check if this location is being used in inventory
        $checkInventory = "SELECT COUNT(*) as count FROM inventory WHERE location_id = :id";
        $checkStmt = $this->db->prepare($checkInventory);
        $checkStmt->execute(['id' => $id]);
        $result = $checkStmt->fetch();

        if ($result['count'] > 0) {
            return false; // Location is in use in inventory
        }

        // Check if this location is being used in stock transactions
        $checkTransactions = "SELECT COUNT(*) as count FROM stock_transactions WHERE location_id = :id";
        $checkStmt = $this->db->prepare($checkTransactions);
        $checkStmt->execute(['id' => $id]);
        $result = $checkStmt->fetch();

        if ($result['count'] > 0) {
            return false; // Location is in use in transactions
        }

        // If not in use, proceed with deletion
        $sql = "DELETE FROM locations WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    private function getLocationById($id)
    {
        $sql = "SELECT * FROM locations WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    private function getDepartments()
    {
        $sql = "SELECT * FROM departments ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getDepartmentById($id)
    {
        $sql = "SELECT * FROM departments WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    private function addDepartment($data)
    {
        $sql = "INSERT INTO departments (name, description) VALUES (:name, :description)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    private function updateDepartment($id, $data)
    {
        $sql = "UPDATE departments SET name = :name, description = :description WHERE id = :id";
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    private function deleteDepartment($id)
    {
        // Check if this department is being used in stock transactions
        $checkTransactions = "SELECT COUNT(*) as count FROM stock_transactions WHERE department_id = :id";
        $checkStmt = $this->db->prepare($checkTransactions);
        $checkStmt->execute(['id' => $id]);
        $result = $checkStmt->fetch();

        if ($result['count'] > 0) {
            return false; // Department is in use in transactions
        }

        // If not in use, proceed with deletion
        $sql = "DELETE FROM departments WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    private function getCategoryCount()
    {
        $sql = "SELECT COUNT(*) as count FROM categories";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    private function getSizeCount()
    {
        $sql = "SELECT COUNT(*) as count FROM product_sizes";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    private function getLocationCount()
    {
        $sql = "SELECT COUNT(*) as count FROM locations";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    private function getDepartmentCount()
    {
        $sql = "SELECT COUNT(*) as count FROM departments";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    // Validation helper for categories
    private function validateCategoryData($data, $isUpdate = false)
    {
        $errors = [];

        // Validate name
        if (empty($data['name'])) {
            $errors[] = "Category name is required.";
        } elseif (strlen($data['name']) > 100) {
            $errors[] = "Category name cannot exceed 100 characters.";
        }

        // Validate description
        if (isset($data['description']) && strlen($data['description']) > 1000) {
            $errors[] = "Category description cannot exceed 1000 characters.";
        }

        // For update operations, validate ID
        if ($isUpdate) {
            if (!isset($data['id']) || !is_numeric($data['id']) || (int)$data['id'] <= 0) {
                $errors[] = "Invalid category ID for update.";
            }
        }

        return $errors;
    }

    // Validation helper for sizes
    private function validateSizeData($size, $isUpdate = false, $id = null)
    {
        $errors = [];

        // Validate size name
        if (empty($size)) {
            $errors[] = "Size name is required.";
        } elseif (strlen($size) > 20) { // Based on product_sizes.size varchar(20)
            $errors[] = "Size name cannot exceed 20 characters.";
        }

        // For update operations, validate ID
        if ($isUpdate) {
            if (!is_numeric($id) || (int)$id <= 0) {
                $errors[] = "Invalid size ID for update.";
            }
        }

        return $errors;
    }

    // Validation helper for locations
    private function validateLocationData($data, $isUpdate = false)
    {
        $errors = [];

        // Validate name
        if (empty($data['name'])) {
            $errors[] = "Location name is required.";
        } elseif (strlen($data['name']) > 100) {
            $errors[] = "Location name cannot exceed 100 characters.";
        }

        // Validate description
        if (isset($data['description']) && strlen($data['description']) > 1000) {
            $errors[] = "Location description cannot exceed 1000 characters.";
        }

        // For update operations, validate ID
        if ($isUpdate) {
            if (!isset($data['id']) || !is_numeric($data['id']) || (int)$data['id'] <= 0) {
                $errors[] = "Invalid location ID for update.";
            }
        }

        return $errors;
    }

    // Validation helper for departments
    private function validateDepartmentData($data, $isUpdate = false)
    {
        $errors = [];

        // Validate name
        if (empty($data['name'])) {
            $errors[] = "Department name is required.";
        } elseif (strlen($data['name']) > 100) {
            $errors[] = "Department name cannot exceed 100 characters.";
        }

        // Validate description
        if (isset($data['description']) && strlen($data['description']) > 1000) {
            $errors[] = "Department description cannot exceed 1000 characters.";
        }

        // For update operations, validate ID
        if ($isUpdate) {
            if (!isset($data['id']) || !is_numeric($data['id']) || (int)$data['id'] <= 0) {
                $errors[] = "Invalid department ID for update.";
            }
        }

        return $errors;
    }

    // Validation helper for sellers
    private function validateSellerData($data, $isUpdate = false)
    {
        $errors = [];

        // Validate name
        if (empty($data['name'])) {
            $errors[] = "Seller name is required.";
        } elseif (strlen($data['name']) > 255) {
            $errors[] = "Seller name cannot exceed 255 characters.";
        }

        // Validate contact_person
        if (isset($data['contact_person']) && strlen($data['contact_person']) > 255) {
            $errors[] = "Contact person name cannot exceed 255 characters.";
        }

        // Validate phone
        if (isset($data['phone']) && strlen($data['phone']) > 50) {
            $errors[] = "Phone number cannot exceed 50 characters.";
        }

        // Validate email
        if (isset($data['email']) && !empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format.";
            } elseif (strlen($data['email']) > 100) {
                $errors[] = "Email cannot exceed 100 characters.";
            }
        }

        // Validate address
        if (isset($data['address']) && strlen($data['address']) > 1000) { // Assuming a reasonable text limit
            $errors[] = "Address cannot exceed 1000 characters.";
        }

        // For update operations, validate ID
        if ($isUpdate) {
            if (!isset($data['id']) || !is_numeric($data['id']) || (int)$data['id'] <= 0) {
                $errors[] = "Invalid seller ID for update.";
            }
        }

        return $errors;
    }

    // Validation helper for types
    private function validateTypeData($data, $isUpdate = false)
    {
        $errors = [];

        // Validate name
        if (empty($data['name'])) {
            $errors[] = "Type name is required.";
        } elseif (strlen($data['name']) > 100) {
            $errors[] = "Type name cannot exceed 100 characters.";
        }

        // Validate category_id
        if (!isset($data['category_id']) || !is_numeric($data['category_id']) || (int)$data['category_id'] <= 0) {
            $errors[] = "Category ID is required and must be a positive integer.";
        }

        // Validate description
        if (isset($data['description']) && strlen($data['description']) > 1000) {
            $errors[] = "Type description cannot exceed 1000 characters.";
        }

        // For update operations, validate ID
        if ($isUpdate) {
            if (!isset($data['id']) || !is_numeric($data['id']) || (int)$data['id'] <= 0) {
                $errors[] = "Invalid type ID for update.";
            }
        }

        return $errors;
    }

    // ==================== SELLERS ====================

    public function sellers()
    {
        $this->requireAuth();

        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF($is_ajax);
            $action = $_POST['action'] ?? '';
            $response = ['success' => false, 'message' => ''];

            if ($action === 'add') {
                $data = [
                    'name' => trim($_POST['name'] ?? ''),
                    'contact_person' => trim($_POST['contact_person'] ?? null),
                    'phone' => trim($_POST['phone'] ?? null),
                    'email' => trim($_POST['email'] ?? null),
                    'address' => trim($_POST['address'] ?? null)
                ];

                $errors = $this->validateSellerData($data);
                if (!empty($errors)) {
                    $response['message'] = implode(' ', $errors);
                } elseif ($this->addSeller($data)) {
                    $this->addLog("Seller added", "Name: " . $data['name']);
                    $response['success'] = true;
                    $response['message'] = 'Seller added successfully';
                    $response['new_item'] = $this->getSellerById($this->db->lastInsertId());
                } else {
                    $response['message'] = 'Failed to add seller';
                }
            } elseif ($action === 'edit') {
                $id = $_POST['id'] ?? 0;
                $data = [
                    'id' => $id, // Pass ID for validation
                    'name' => trim($_POST['name'] ?? ''),
                    'contact_person' => trim($_POST['contact_person'] ?? null),
                    'phone' => trim($_POST['phone'] ?? null),
                    'email' => trim($_POST['email'] ?? null),
                    'address' => trim($_POST['address'] ?? null)
                ];

                $errors = $this->validateSellerData($data, true); // true for update
                if (!empty($errors)) {
                    $response['message'] = implode(' ', $errors);
                } elseif ($this->updateSeller($id, $data)) {
                    $this->addLog("Seller updated", "ID: $id, Name: " . $data['name']);
                    $response['success'] = true;
                    $response['message'] = 'Seller updated successfully';
                    $response['updated_item'] = $this->getSellerById($id);
                } else {
                    $response['message'] = 'Failed to update seller';
                }
            } elseif ($action === 'delete') {
                $id = (int)($_POST['id'] ?? 0);

                $errors = $this->validateSellerData(['id' => $id], true); // Validate ID for delete
                if (!empty($errors)) {
                    $response['message'] = implode(' ', $errors);
                } else {
                    $deleteResult = $this->deleteSeller($id);
                    if ($deleteResult === true) {
                        $this->addLog("Seller deleted", "ID: $id");
                        $response['success'] = true;
                        $response['message'] = 'Seller deleted successfully';
                        $response['deleted_id'] = $id;
                    } else {
                        $response['message'] = $deleteResult ?: 'Failed to delete seller. It may be referenced in transactions.';
                    }
                }
            }

            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                // For non-AJAX requests, redirect as before
                if ($response['success']) {
                    $_SESSION['success'] = $response['message'];
                } else {
                    $_SESSION['error'] = $response['message'];
                }
                $this->redirect('/master-data/sellers');
            }
        }

        $edit_id = $_GET['edit_id'] ?? null;
        $editing_seller = null;
        if ($edit_id) {
            $editing_seller = $this->getSellerById($edit_id);
        }

        $data = [
            'sellers' => $this->getSellers(),
            'editing_seller' => $editing_seller
        ];

        $this->view('config/sellers', $data);
    }

    private function getSellers()
    {
        $sql = "SELECT * FROM sellers ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getSellerById($id)
    {
        $sql = "SELECT * FROM sellers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    private function addSeller($data)
    {
        $sql = "INSERT INTO sellers (name, contact_person, phone, email, address) VALUES (:name, :contact_person, :phone, :email, :address)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    private function updateSeller($id, $data)
    {
        $sql = "UPDATE sellers SET name = :name, contact_person = :contact_person, phone = :phone, email = :email, address = :address WHERE id = :id";
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    private function deleteSeller($id)
    {
        // Check if this seller is being used in stock transactions
        $checkTransactions = "SELECT COUNT(*) as count FROM stock_transactions WHERE seller_id = :id";
        $checkStmt = $this->db->prepare($checkTransactions);
        $checkStmt->execute(['id' => $id]);
        $result = $checkStmt->fetch();

        if ($result['count'] > 0) {
            return false; // Seller is in use in transactions
        }

        // If not in use, proceed with deletion
        $sql = "DELETE FROM sellers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    private function getSellerCount()
    {
        $sql = "SELECT COUNT(*) as count FROM sellers";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    // ==================== TYPES ====================

    public function types()
    {
        $this->requireAuth();

        $typeModel = $this->model('TypeModel');
        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF($is_ajax);
            $action = $_POST['action'] ?? '';
            $response = ['success' => false, 'message' => ''];

            if ($action === 'add') {
                $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
                $data = [
                    'name' => trim($_POST['name'] ?? ''),
                    'category_id' => $categoryId,
                    'description' => trim($_POST['description'] ?? null)
                ];

                $errors = $this->validateTypeData($data);
                if (!empty($errors)) {
                    $response['message'] = implode(' ', $errors);
                } elseif ($typeModel->create($data)) {
                    $this->addLog("Type added", "Name: " . $data['name']);
                    $response['success'] = true;
                    $response['message'] = 'Type added successfully';
                    $response['new_item'] = $typeModel->getById($this->db->lastInsertId());
                } else {
                    $response['message'] = 'Failed to add type';
                }
            } elseif ($action === 'edit') {
                $id = (int)($_POST['id'] ?? 0);
                $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
                $data = [
                    'id' => $id, // Pass ID for validation
                    'name' => trim($_POST['name'] ?? ''),
                    'category_id' => $categoryId,
                    'description' => trim($_POST['description'] ?? null)
                ];

                $errors = $this->validateTypeData($data, true); // true for update
                if (!empty($errors)) {
                    $response['message'] = implode(' ', $errors);
                } elseif ($typeModel->update($id, $data)) {
                    $this->addLog("Type updated", "ID: $id, Name: " . $data['name']);
                    $response['success'] = true;
                    $response['message'] = 'Type updated successfully';
                    $response['updated_item'] = $typeModel->getById($id);
                } else {
                    $response['message'] = 'Failed to update type';
                }
            } elseif ($action === 'delete') {
                $id = (int)($_POST['id'] ?? 0);

                // For delete, we only need to validate the ID.
                if ($id <= 0) {
                    $response['message'] = 'Invalid type ID for delete.';
                } else {
                    $deleteResult = $typeModel->delete($id);
                    if ($deleteResult === true) {
                        $this->addLog("Type deleted", "ID: $id");
                        $response['success'] = true;
                        $response['message'] = 'Type deleted successfully';
                        $response['deleted_id'] = $id;
                    } else {
                        $response['message'] = is_string($deleteResult) ? $deleteResult : 'Failed to delete type.';
                    }
                }
            }

            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                if ($response['success']) {
                    $_SESSION['success'] = $response['message'];
                } else {
                    $_SESSION['error'] = $response['message'];
                }
                $this->redirect('/master-data/types');
            }
        }

        $edit_id = $_GET['edit_id'] ?? null;
        $editing_type = null;
        if ($edit_id) {
            $editing_type = $typeModel->getById($edit_id);
        }

        $data = [
            'types' => $typeModel->getAll(),
            'editing_type' => $editing_type,
            'categories' => $this->getCategories()
        ];

        $this->view('config/types', $data);
    }
}