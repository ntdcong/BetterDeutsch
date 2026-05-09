<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Verb
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function findByInfinitive(string $infinitive): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM verbs WHERE Infinitive = :infinitive LIMIT 1");
        $stmt->execute(['infinitive' => $infinitive]);
        return $stmt->fetch();
    }
}
