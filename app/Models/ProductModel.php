<?php

namespace App\Models;

use App\Core\Model;

class ProductModel extends Model
{
    protected $table = 'products';

    public function getAll()
    {
        $sql = "SELECT p.id, p.part_number, p.description, p.barcode, p.qr_code, p.low_stock_threshold, p.available_sizes,
                       c.name as category_name, t.name as type_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN types t ON p.type_id = t.id
                WHERE p.deleted_at IS NULL ORDER BY p.part_number";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllWithCategory()
    {
        $sql = "SELECT p.id, p.part_number, p.description, p.barcode, p.qr_code, p.low_stock_threshold, p.available_sizes,
                       c.name as category_name, t.name as type_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN types t ON p.type_id = t.id
                WHERE p.deleted_at IS NULL
                ORDER BY p.part_number";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByPartNumber($partNumber)
    {
        $sql = "SELECT p.*, c.name as category_name, t.name as type_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN types t ON p.type_id = t.id
                WHERE p.part_number = :part_number AND p.deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['part_number' => $partNumber]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $sql = "INSERT INTO products (part_number, type_id, category_id, description, low_stock_threshold, qr_code, available_sizes) 
                VALUES (:part_number, :type_id, :category_id, :description, :low_stock_threshold, :qr_code, :available_sizes)";

        $stmt = $this->db->prepare($sql);
        // Ensure 'product_type' is not in $data if it's being passed
        unset($data['product_type']);
        return $stmt->execute($data);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE products SET 
                part_number = :part_number,
                type_id = :type_id,
                category_id = :category_id,
                description = :description,
                low_stock_threshold = :low_stock_threshold,
                qr_code = :qr_code,
                available_sizes = :available_sizes
                WHERE id = :id";

        $data['id'] = $id;
        // Ensure 'product_type' is not in $data if it's being passed
        unset($data['product_type']);
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getProductDetails($productId)
    {
        $sql = "SELECT p.*, c.name as category_name, t.name as type_name,
                (SELECT SUM(quantity) FROM inventory WHERE product_id = p.id) as total_stock
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN types t ON p.type_id = t.id
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $productId]);
        return $stmt->fetch();
    }

    public function getProductInventory($productId)
    {
        $sql = "SELECT i.quantity, i.min_quantity, i.last_updated, l.name as location_name, ps.size 
                FROM inventory i
                JOIN locations l ON i.location_id = l.id
                LEFT JOIN product_sizes ps ON i.size_id = ps.id
                WHERE i.product_id = :product_id
                ORDER BY l.name, ps.size";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function getAvailableSizes($productId)
    {
        $sql = "SELECT available_sizes FROM products WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $productId]);
        $result = $stmt->fetch();

        if (!$result || empty($result['available_sizes'])) {
            return [];
        }

        $sizeIds = explode(',', $result['available_sizes']);
        $placeholders = str_repeat('?,', count($sizeIds) - 1) . '?';

        $sql = "SELECT id, size FROM product_sizes WHERE id IN ($placeholders) ORDER BY id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($sizeIds);

        return $stmt->fetchAll();
    }

    public function getAvailableSizesInStock($productId, $locationId)
    {
        $sql = "SELECT ps.id, ps.size 
                FROM inventory i
                JOIN product_sizes ps ON i.size_id = ps.id
                WHERE i.product_id = :product_id 
                AND i.location_id = :location_id
                AND i.quantity > 0
                ORDER BY ps.id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['product_id' => $productId, 'location_id' => $locationId]);

        return $stmt->fetchAll();
    }

    public function delete($id)
    {
        // Soft delete - set deleted_at timestamp
        $sql = "UPDATE products SET deleted_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function searchByPartNumber($term)
    {
        $sql = "SELECT p.id, p.part_number, p.description, p.barcode, p.qr_code, p.low_stock_threshold, p.available_sizes,
                       c.name as category_name, t.name as type_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN types t ON p.type_id = t.id
                WHERE p.deleted_at IS NULL
                AND (p.part_number LIKE :term OR p.description LIKE :term)
                ORDER BY p.part_number";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['term' => '%' . $term . '%']);
        return $stmt->fetchAll();
    }
}
