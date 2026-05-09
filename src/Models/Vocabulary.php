<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Vocabulary
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function getByNotebookId(int $notebookId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM vocabularies WHERE notebook_id = :notebook_id ORDER BY created_at DESC");
        $stmt->execute(['notebook_id' => $notebookId]);
        return $stmt->fetchAll();
    }

    public function getPaginatedForNotebook(int $notebookId, int $page = 1, int $perPage = 20, string $search = ''): array
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM vocabularies WHERE notebook_id = :notebook_id";
        $params = ['notebook_id' => $notebookId];

        if ($search !== '') {
            $sql .= " AND (word LIKE :search1 OR translation_vn LIKE :search2)";
            $params['search1'] = '%' . $search . '%';
            $params['search2'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getTotalCount(int $notebookId, string $search = ''): int
    {
        $sql = "SELECT COUNT(*) FROM vocabularies WHERE notebook_id = :notebook_id";
        $params = ['notebook_id' => $notebookId];

        if ($search !== '') {
            $sql .= " AND (word LIKE :search1 OR translation_vn LIKE :search2)";
            $params['search1'] = '%' . $search . '%';
            $params['search2'] = '%' . $search . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM vocabularies WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): int|false
    {
        $sql = "INSERT INTO vocabularies (word, translation_vn, word_type, article, plural_form, preposition, note, user_id, notebook_id) 
                VALUES (:word, :translation_vn, :word_type, :article, :plural_form, :preposition, :note, :user_id, :notebook_id)";
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute($data)) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE vocabularies SET 
                word = :word, translation_vn = :translation_vn, word_type = :word_type, 
                article = :article, plural_form = :plural_form, preposition = :preposition, 
                note = :note WHERE id = :id";
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM vocabularies WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
