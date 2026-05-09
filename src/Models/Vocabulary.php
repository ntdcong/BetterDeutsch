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
        $stmt = $this->db->prepare("SELECT * FROM vocabularies WHERE notebook_id = :notebook_id");
        $stmt->execute(['notebook_id' => $notebookId]);
        return $stmt->fetchAll();
    }
}
