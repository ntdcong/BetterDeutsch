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

    public function getAllPublic(): array
    {
        $sql = "SELECT n.*, (SELECT COUNT(*) FROM vocabularies v WHERE v.notebook_id = n.id) as count FROM notebooks n WHERE n.is_public = 1 OR n.is_admin_updated = 1 ORDER BY n.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT n.*, g.name as group_name, (SELECT COUNT(*) FROM vocabularies v WHERE v.notebook_id = n.id) as count FROM notebooks n LEFT JOIN notebook_groups g ON n.notebook_group_id = g.id WHERE n.id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create(int $userId, string $name, ?string $note, ?int $groupId, int $isPublic = 0, int $isAdminUpdated = 0): int|false
    {
        $stmt = $this->db->prepare("INSERT INTO notebooks (user_id, name, note, notebook_group_id, is_public, is_admin_updated) VALUES (:user_id, :name, :note, :group_id, :is_public, :is_admin_updated)");
        if ($stmt->execute([
            'user_id' => $userId,
            'name' => $name,
            'note' => $note,
            'group_id' => $groupId,
            'is_public' => $isPublic,
            'is_admin_updated' => $isAdminUpdated
        ])) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    public function update(int $id, int $userId, string $name, ?string $note, ?int $groupId, int $isPublic = 0, int $isAdminUpdated = 0, bool $isAdmin = false): bool
    {
        if ($isAdmin) {
            $stmt = $this->db->prepare("UPDATE notebooks SET name = :name, note = :note, notebook_group_id = :group_id, is_public = :is_public, is_admin_updated = :is_admin_updated WHERE id = :id");
            return $stmt->execute([
                'id' => $id,
                'name' => $name,
                'note' => $note,
                'group_id' => $groupId,
                'is_public' => $isPublic,
                'is_admin_updated' => $isAdminUpdated
            ]);
        } else {
            $stmt = $this->db->prepare("UPDATE notebooks SET name = :name, note = :note, notebook_group_id = :group_id WHERE id = :id AND user_id = :user_id");
            return $stmt->execute([
                'id' => $id,
                'user_id' => $userId,
                'name' => $name,
                'note' => $note,
                'group_id' => $groupId
            ]);
        }
    }

    public function delete(int $id, int $userId, bool $isAdmin = false): bool
    {
        if ($isAdmin) {
            $stmt = $this->db->prepare("DELETE FROM notebooks WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } else {
            $stmt = $this->db->prepare("DELETE FROM notebooks WHERE id = :id AND user_id = :user_id");
            return $stmt->execute(['id' => $id, 'user_id' => $userId]);
        }
    }

    public function findByShareToken(string $token): array|false
    {
        $stmt = $this->db->prepare("SELECT n.*, g.name as group_name, (SELECT COUNT(*) FROM vocabularies v WHERE v.notebook_id = n.id) as count FROM notebooks n LEFT JOIN notebook_groups g ON n.notebook_group_id = g.id WHERE n.share_token = :token AND n.is_shared = 1 LIMIT 1");
        $stmt->execute(['token' => $token]);
        return $stmt->fetch();
    }

    public function toggleShare(int $id, int $userId): ?string
    {
        // Get current status
        $stmt = $this->db->prepare("SELECT is_shared FROM notebooks WHERE id = :id AND user_id = :user_id LIMIT 1");
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $row = $stmt->fetch();

        if (!$row) return null; // Not found or not owner

        if ($row['is_shared'] == 1) {
            // Turn off
            $update = $this->db->prepare("UPDATE notebooks SET is_shared = 0, share_token = NULL WHERE id = :id");
            $update->execute(['id' => $id]);
            return "";
        } else {
            // Turn on
            $token = bin2hex(random_bytes(16));
            $update = $this->db->prepare("UPDATE notebooks SET is_shared = 1, share_token = :token WHERE id = :id");
            $update->execute(['id' => $id, 'token' => $token]);
            return $token;
        }
    }
}
