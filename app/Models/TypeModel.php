<?php

namespace App\Models;

use App\Core\Model;

class TypeModel extends Model
{
    protected $table = 'types';

    public function create($data)
    {
        $sql = "INSERT INTO types (name, category_id, description) 
                VALUES (:name, :category_id, :description)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE types SET 
                name = :name,
                category_id = :category_id,
                description = :description
                WHERE id = :id";

        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getByName($name)
    {
        $sql = "SELECT * FROM types WHERE name = :name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['name' => $name]);
        return $stmt->fetch();
    }

    public function getCount()
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function getAll()
    {
        $sql = "SELECT t.*, c.name as category_name 
                FROM {$this->table} t
                LEFT JOIN categories c ON t.category_id = c.id
                ORDER BY t.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT t.*, c.name as category_name 
                FROM {$this->table} t
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE t.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getByCategory($category_id)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE category_id = :category_id 
                ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['category_id' => $category_id]);
        return $stmt->fetchAll();
    }

    public function delete($id)
    {
        // Check if this type is being used by any products
        $checkSql = "SELECT COUNT(*) as count FROM products WHERE type_id = :id";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute(['id' => $id]);
        $result = $checkStmt->fetch();

        if ($result['count'] > 0) {
            return "This type is in use by products and cannot be deleted.";
        }

        // If not in use, proceed with deletion
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
