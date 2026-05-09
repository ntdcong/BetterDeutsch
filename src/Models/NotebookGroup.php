<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class NotebookGroup
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function getAllForUser(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM notebook_groups WHERE user_id = :user_id OR user_id IS NULL ORDER BY name ASC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function create(int $userId, string $name): int|false
    {
        $stmt = $this->db->prepare("INSERT INTO notebook_groups (user_id, name) VALUES (:user_id, :name)");
        if ($stmt->execute(['user_id' => $userId, 'name' => $name])) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM notebook_groups WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    public function update(int $id, int $userId, string $name): bool
    {
        $stmt = $this->db->prepare("UPDATE notebook_groups SET name = :name WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['name' => $name, 'id' => $id, 'user_id' => $userId]);
    }
}
