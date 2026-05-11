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
        $sql = "SELECT n.*, g.name as group_name, (SELECT COUNT(*) FROM vocabularies v WHERE v.notebook_id = n.id) as count FROM notebooks n LEFT JOIN notebook_groups g ON n.notebook_group_id = g.id WHERE n.user_id = :user_id OR n.is_public = 1 OR n.is_admin_updated = 1 ORDER BY n.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT n.*, g.name as group_name, (SELECT COUNT(*) FROM vocabularies v WHERE v.notebook_id = n.id) as count FROM notebooks n LEFT JOIN notebook_groups g ON n.notebook_group_id = g.id WHERE n.id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create(int $userId, string $name, ?string $note, ?int $groupId): int|false
    {
        $stmt = $this->db->prepare("INSERT INTO notebooks (user_id, name, note, notebook_group_id) VALUES (:user_id, :name, :note, :group_id)");
        if ($stmt->execute([
            'user_id' => $userId,
            'name' => $name,
            'note' => $note,
            'group_id' => $groupId
        ])) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    public function update(int $id, int $userId, string $name, ?string $note, ?int $groupId): bool
    {
        $stmt = $this->db->prepare("UPDATE notebooks SET name = :name, note = :note, notebook_group_id = :group_id WHERE id = :id AND user_id = :user_id");
        return $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
            'name' => $name,
            'note' => $note,
            'group_id' => $groupId
        ]);
    }

    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM notebooks WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }
}
