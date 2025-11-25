<?php

namespace App\Models;
use PDO;

use App\Core\Model;

class LogModel extends Model
{
    protected $table = 'activity_logs';

    public function addLog($userId, $username, $action, $details = '', $ip = null)
    {
        $sql = "INSERT INTO activity_logs (user_id, username, action, details, ip) 
                VALUES (:user_id, :username, :action, :details, :ip)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(
            [
            'user_id' => $userId,
            'username' => $username,
            'action' => $action,
            'details' => $details,
            'ip' => $ip
            ]
        );
    }

    public function getLogs($limit = 100, $offset = 0)
    {
        $sql = "SELECT al.id, al.created_at, al.action, al.details, al.ip, u.full_name AS username 
                FROM activity_logs al
                LEFT JOIN users u ON al.user_id = u.id
                ORDER BY al.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function searchLogs($searchTerm, $dateFrom = null, $dateTo = null)
    {
        $sql = "SELECT al.id, al.created_at, al.action, al.details, al.ip, u.full_name AS username 
                FROM activity_logs al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE (al.action LIKE :search OR al.details LIKE :search)";

        $params = ['search' => '%' . $searchTerm . '%'];

        if ($dateFrom) {
            $sql .= " AND DATE(al.created_at) >= :date_from";
            $params['date_from'] = $dateFrom;
        }

        if ($dateTo) {
            $sql .= " AND DATE(al.created_at) <= :date_to";
            $params['date_to'] = $dateTo;
        }

        $sql .= " ORDER BY al.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getLogsByCategory($category, $limit = 100, $offset = 0)
    {
        $actions = [];
        switch ($category) {
        case 'user':
            $actions = ['User login', 'User logout'];
            break;
        case 'product':
            $actions = ['Product added', 'Product updated', 'Product deleted'];
            break;
        case 'stock':
            $actions = ['Stock added', 'Stock removed', 'Stock transferred'];
            break;
        case 'report':
            $actions = ['Report generated'];
            break;
        case 'system':
            $actions = ['Logs cleared'];
            break;
        default:
            return $this->getLogs($limit, $offset);
        }

        if (empty($actions)) {
            return [];
        }

        $inParams = [];
        $placeholders = [];
        foreach ($actions as $i => $action) {
            $key = ":action" . $i;
            $placeholders[] = $key;
            $inParams[$key] = $action;
        }
        $placeholders_string = implode(',', $placeholders);

        $sql = "SELECT al.id, al.created_at, al.action, al.details, al.ip, u.full_name AS username 
                FROM activity_logs al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.action IN ($placeholders_string)
                ORDER BY al.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($inParams as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }
}
