<?php
namespace Core\Database\Drivers;

use PDO;
use Exception;

class MysqlDriver implements DriverInterface
{
    private PDO $pdo;

    public function __construct(array $config)
    {
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 3306;
        $db      = $config['database'] ?? null;
        $user    = $config['username'] ?? 'root';
        $pass    = $config['password'] ?? '';
        $charset = $config['charset'] ?? 'utf8mb4';

        $dsn = "mysql:host={$host};port={$port};charset={$charset}";
        if ($db) {
            $dsn .= ";dbname={$db}";
        }
        
        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (\PDOException $e) {
            throw new Exception("MySQL Connection failed: " . $e->getMessage());
        }
    }

    public function connect(): PDO
    {
        return $this->pdo;
    }

    public function query(string $sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
