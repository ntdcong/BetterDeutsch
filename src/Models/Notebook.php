<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Notebook
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function getAllForUser(int $userId): array
    {
        $sql = "SELECT * FROM notebooks WHERE user_id = :user_id OR is_public = 1 OR is_admin_updated = 1 ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM notebooks WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}
