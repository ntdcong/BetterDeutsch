<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private ?PDO $connection = null;

    public function __construct()
    {
        $config = require BASE_PATH . '/config/database.php';
        
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        try {
            $this->connection = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau.");
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
