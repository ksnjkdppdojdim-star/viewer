<?php
namespace Core\Database\Drivers;

use PDO;

interface DriverInterface
{
    /**
     * Initialize connection using PDO
     */
    public function connect(): PDO;
    
    /**
     * Execute a raw SQL query
     */
    public function query(string $sql, array $params = []);
    
    /**
     * Get the PDO instance
     */
    public function getPdo(): PDO;
}
