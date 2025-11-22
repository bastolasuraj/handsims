<?php

namespace App\Models;
use PDO;

use App\Core\Model;

class TransactionModel extends Model
{
    protected $table = 'stock_transactions';

    public function addTransaction($data)
    {
        $sql = "INSERT INTO stock_transactions 
                (transaction_type, product_id, location_id, department_id, size_id, quantity, user_id, remarks, seller_id, price_per_unit) 
                VALUES 
                (:transaction_type, :product_id, :location_id, :department_id, :size_id, :quantity, :user_id, :remarks, :seller_id, :price_per_unit)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getTransactionHistory($filters = [])
    {
        $sql = "SELECT st.transaction_date, st.transaction_type, p.part_number, t.name as product_type, 
                l.name as location_name, d.name as department_name,
                ps.size, st.quantity, u.username, st.remarks, s.name as seller_name, st.price_per_unit
                FROM stock_transactions st
                JOIN products p ON st.product_id = p.id
                JOIN locations l ON st.location_id = l.id
                LEFT JOIN departments d ON st.department_id = d.id
                LEFT JOIN product_sizes ps ON st.size_id = ps.id
                LEFT JOIN users u ON st.user_id = u.id
                LEFT JOIN types t ON p.type_id = t.id
                LEFT JOIN sellers s ON st.seller_id = s.id
                WHERE 1=1";
        $params = [];
        if (!empty($filters['product_id'])) {
            $sql .= " AND st.product_id = :product_id";
            $params['product_id'] = $filters['product_id'];
        }

        if (!empty($filters['location_id'])) {
            $sql .= " AND st.location_id = :location_id";
            $params['location_id'] = $filters['location_id'];
        }

        if (!empty($filters['department_id'])) {
            $sql .= " AND st.department_id = :department_id";
            $params['department_id'] = $filters['department_id'];
        }

        if (!empty($filters['transaction_type'])) {
            $sql .= " AND st.transaction_type = :transaction_type";
            $params['transaction_type'] = $filters['transaction_type'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(st.transaction_date) >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(st.transaction_date) <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        $sql .= " ORDER BY st.transaction_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getProductTransactions($productId)
    {
        $sql = "SELECT st.transaction_date, st.transaction_type, l.name as location_name, d.name as department_name,
                ps.size, st.quantity, u.username, st.remarks
                FROM stock_transactions st
                JOIN locations l ON st.location_id = l.id
                LEFT JOIN departments d ON st.department_id = d.id
                LEFT JOIN product_sizes ps ON st.size_id = ps.id
                LEFT JOIN users u ON st.user_id = u.id
                WHERE st.product_id = :product_id
                ORDER BY st.transaction_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function getTodaysTransactionCount()
    {
        $sql = "SELECT COUNT(*) as count FROM stock_transactions WHERE DATE(transaction_date) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    }
}
