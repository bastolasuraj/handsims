<?php

namespace App\Models;
use PDO;
use Throwable;

use App\Core\Model;

class InventoryModel extends Model
{
    protected $table = 'inventory';

    public function getStockByLocation($locationId = null)
    {
        $sql = "SELECT i.product_id, i.location_id, i.size_id, p.part_number, t.name as product_type, p.description as product_desc, p.low_stock_threshold, p.deleted_at,
                l.name as location_name, ps.size, c.name as category_name,
                i.quantity, i.min_quantity, i.last_updated
                FROM inventory i
                JOIN products p ON i.product_id = p.id
                JOIN locations l ON i.location_id = l.id
                LEFT JOIN product_sizes ps ON i.size_id = ps.id
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN types t ON p.type_id = t.id";

        if ($locationId) {
            $sql .= " WHERE i.location_id = :location_id";
        }

        $sql .= " ORDER BY l.name, p.part_number, ps.size";

        $stmt = $this->db->prepare($sql);

        if ($locationId) {
            $stmt->execute(['location_id' => $locationId]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll();
    }

    public function updateStock($productId, $locationId, $sizeId, $quantity, $type = 'add')
    {
        // Use atomic database operations to prevent race conditions.

        // Sanitize quantity to ensure it's a positive integer.
        $quantity = abs((int)$quantity);

        // For subtractions, use the new subtractStock method
        if ($type !== 'add') {
            $result = $this->subtractStock($productId, $locationId, $sizeId, $quantity);
            return $result['ok'];
        }

        // For additions, use INSERT ... ON DUPLICATE KEY UPDATE for atomicity.
        // This requires a UNIQUE constraint on (product_id, location_id, size_id).
        // Assumes such a constraint exists.
        $sql = "INSERT INTO inventory (product_id, location_id, size_id, quantity) 
                VALUES (:product_id, :location_id, :size_id, :quantity)
                ON DUPLICATE KEY UPDATE quantity = quantity + :quantity";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(
            [
            'product_id' => $productId,
            'location_id' => $locationId,
            'size_id' => ($sizeId === '' ? null : $sizeId),
            'quantity' => $quantity
            ]
        );
    }

    /**
     * Subtract stock with proper validation and locking
     * Fails if insufficient stock available
     */
    public function subtractStock(int $productId, int $locationId, ?int $sizeId, int $quantity): array
    {
        $db = $this->db;

        // Check if a transaction is already active
        $transactionStartedHere = false;
        if (!$db->inTransaction()) {
            $db->beginTransaction();
            $transactionStartedHere = true;
        }

        try {
            // Lock the row for update to prevent race conditions
            $sql = "SELECT id, quantity FROM inventory
                    WHERE product_id = :p AND location_id = :l
                    AND " . ($sizeId ? "size_id = :s" : "size_id IS NULL") . "
                    FOR UPDATE";

            $stmt = $db->prepare($sql);
            $params = ['p' => $productId, 'l' => $locationId];
            if ($sizeId) {
                $params['s'] = $sizeId;
            }
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if we have sufficient stock
            if (!$row || (int)$row['quantity'] < $quantity) {
                if ($transactionStartedHere) {
                    $db->rollBack();
                }
                $available = $row ? (int)$row['quantity'] : 0;
                return [
                    'ok' => false,
                    'error' => 'INSUFFICIENT_STOCK',
                    'available' => $available,
                    'requested' => $quantity
                ];
            }

            // Update the quantity
            $upd = $db->prepare("UPDATE inventory SET quantity = quantity - :q WHERE id = :id");
            $upd->execute(['q' => $quantity, 'id' => $row['id']]);

            if ($transactionStartedHere) {
                $db->commit();
            }
            return ['ok' => true];
        } catch (Throwable $e) {
            if ($transactionStartedHere) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public function getLowStockItems()
    {
        $sql = "SELECT i.product_id, p.part_number, t.name as product_type, l.name as location_name, ps.size, i.quantity, i.min_quantity, i.remarks, i.last_updated
                FROM inventory i
                JOIN products p ON i.product_id = p.id
                JOIN locations l ON i.location_id = l.id
                LEFT JOIN product_sizes ps ON i.size_id = ps.id
                LEFT JOIN types t ON p.type_id = t.id
                WHERE i.quantity <= i.min_quantity
                ORDER BY i.quantity ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getLowStockItemsDetailed($locationId = null)
    {
        $sql = "SELECT i.product_id, i.location_id, i.size_id, p.part_number, t.name as product_type, l.name as location_name, ps.size, i.quantity, i.min_quantity, p.low_stock_threshold, i.remarks, i.last_updated
                FROM inventory i
                JOIN products p ON i.product_id = p.id
                JOIN locations l ON i.location_id = l.id
                LEFT JOIN product_sizes ps ON i.size_id = ps.id
                LEFT JOIN types t ON p.type_id = t.id
                WHERE i.quantity <= p.low_stock_threshold";

        $params = [];
        if ($locationId) {
            $sql .= " AND i.location_id = :location_id";
            $params['location_id'] = $locationId;
        }

        $sql .= " ORDER BY i.quantity ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getTotalStockQuantity()
    {
        $sql = "SELECT SUM(quantity) as total_quantity FROM inventory";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total_quantity'] ?? 0);
    }
}
