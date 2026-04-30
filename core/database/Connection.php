<?php
namespace Core\Database;

use Core\Database\Drivers\MysqlDriver;
use Core\Database\Drivers\PgsqlDriver;
use Exception;

class Connection
{
    private static $instances = [];

    /**
     * Get or create a database connection instance based on the provided config
     */
    public static function make(array $config, string $name = 'default')
    {
        if (isset(self::$instances[$name])) {
            return self::$instances[$name];
        }

        $driverName = strtolower($config['driver'] ?? '');

        try {
            $driver = self::createDriver($driverName, $config);
        } catch (Exception $e) {
            if (!self::canCreateMissingDatabase($driverName, $config, $e->getMessage())) {
                throw $e;
            }

            self::createDatabase($driverName, $config);
            $driver = self::createDriver($driverName, $config);
        }

        self::$instances[$name] = $driver;
        return $driver;
    }

    private static function createDriver(string $driverName, array $config)
    {
        switch ($driverName) {
            case 'mysql':
            case 'mariadb':
                return new MysqlDriver($config);
            case 'pgsql':
            case 'postgres':
                return new PgsqlDriver($config);
            default:
                throw new Exception("Database driver '{$driverName}' is not supported.");
        }
    }

    private static function canCreateMissingDatabase(string $driverName, array $config, string $message): bool
    {
        if (empty($config['database'])) {
            return false;
        }

        if (in_array($driverName, ['mysql', 'mariadb'], true)) {
            return stripos($message, 'Unknown database') !== false;
        }

        if (in_array($driverName, ['pgsql', 'postgres'], true)) {
            return stripos($message, 'does not exist') !== false
                || stripos($message, "n'existe pas") !== false
                || stripos($message, 'existe pas') !== false;
        }

        return false;
    }

    private static function createDatabase(string $driverName, array $config): void
    {
        $database = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $config['database']);
        if ($database === '') {
            throw new Exception('Cannot create database: invalid database name.');
        }

        $serverConfig = $config;
        unset($serverConfig['database']);

        if (in_array($driverName, ['pgsql', 'postgres'], true)) {
            $server = new PgsqlDriver($serverConfig);
            $quoted = '"' . str_replace('"', '""', $database) . '"';
            $server->query("CREATE DATABASE {$quoted}");
            return;
        }

        $server = new MysqlDriver($serverConfig);
        $server->query("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }
}
