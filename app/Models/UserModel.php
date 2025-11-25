<?php

namespace App\Models;
use PDO;

use App\Core\Model;

class UserModel extends Model
{
    protected $table = 'users';

    public function authenticate($username, $password)
    {
        $sql = "SELECT * FROM users WHERE username = :username AND auth_type = 'local'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function getByUsername($username)
    {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    public function createFromAD($data)
    {
        $sql = "INSERT INTO users (username, email, full_name, department_id, auth_type, role, created_at) 
                VALUES (:username, :email, :full_name, :department_id, :auth_type, 'user', NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function updateFromAD($userId, $data)
    {
        $sql = "UPDATE users SET 
                email = :email,
                full_name = :full_name,
                department_id = :department_id,
                updated_at = NOW()
                WHERE id = :id";
        $data['id'] = $userId;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function create($data)
    {
        $sql = "INSERT INTO users (username, password, email, full_name, role) 
                VALUES (:username, :password, :email, :full_name, :role)";
        $stmt = $this->db->prepare($sql);
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $stmt->execute($data);
    }

    public function updatePassword($userId, $newPassword)
    {
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(
            [
            'id' => $userId,
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
            ]
        );
    }

    public function getDepartmentIdByName($departmentName)
    {
        if (empty($departmentName)) {
            return null;
        }
        $sql = "SELECT id FROM departments WHERE name = :name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['name' => $departmentName]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    }
}
