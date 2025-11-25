<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class SchemaModel extends Model
{
    public function getTables()
    {
        $stmt = $this->db->prepare("SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getTableSchema($tableName)
    {
        $stmt = $this->db->prepare("SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_KEY, COLUMN_DEFAULT, EXTRA FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
        $stmt->execute([$tableName]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
