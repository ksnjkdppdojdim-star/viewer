<?php
namespace Core\Install;

use Core\Database\Connection;
use Core\Database\Drivers\PgsqlDriver;
use Core\Database\Migrator;
use Core\Auth\AdminUserService;
use Exception;

class Installer
{
    public function checkRequirements(): array
    {
        return [
            'extensions' => [
                'PDO' => extension_loaded('pdo'),
                'PDO MySQL' => extension_loaded('pdo_mysql'),
                'PDO PostgreSQL' => extension_loaded('pdo_pgsql'),
                'MBString' => extension_loaded('mbstring'),
                'JSON' => extension_loaded('json'),
            ],
            'permissions' => [
                'config/' => is_writable(VIEWER_ROOT . '/config'),
                'storage/' => is_writable(VIEWER_ROOT . '/storage'),
                'organizations/' => is_writable(VIEWER_ROOT . '/organizations'),
            ],
            'php_version' => version_compare(PHP_VERSION, '8.1.0', '>='),
        ];
    }

    public function getDebugInfo(): array
    {
        return [
            'php_ini' => php_ini_loaded_file(),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Inconnu',
            'os' => PHP_OS,
        ];
    }

    public function testConnection(array $config)
    {
        try {
            Connection::make($config, 'temp_install');
            return true;
        } catch (Exception $e) {
            $msg = $e->getMessage();

            if (strpos($msg, 'Unknown database') !== false || strpos($msg, 'does not exist') !== false) {
                return $this->createDatabaseIfMissing($config);
            }

            return $msg;
        }
    }

    public function runMigrations(array $dbConfig): void
    {
        $db = Connection::make($dbConfig, 'system');
        $migrator = new Migrator($db);
        $migrator->run('system');
    }

    public function createAdmin(array $dbConfig, string $email, string $password): void
    {
        (new AdminUserService())->createOrUpdate($dbConfig, $email, $password, 'admin');
    }

    public function finalize(): void
    {
        file_put_contents(VIEWER_ROOT . '/config/installed.txt', date('Y-m-d H:i:s'), LOCK_EX);
    }

    private function createDatabaseIfMissing(array $config)
    {
        try {
            $driver = strtolower((string) ($config['driver'] ?? 'mysql'));
            $dbName = preg_replace('/[^a-zA-Z0-9_]/', '', (string) ($config['database'] ?? ''));
            if ($dbName === '') {
                return "Nom de base de données invalide.";
            }

            $noDbConfig = $config;
            unset($noDbConfig['database']);
            $tempConn = Connection::make($noDbConfig, 'server_only');

            if ($tempConn instanceof PgsqlDriver || $driver === 'pgsql' || $driver === 'postgres') {
                $stmt = $tempConn->query('SELECT 1 FROM pg_database WHERE datname = :name', ['name' => $dbName]);
                if (!$stmt->fetch()) {
                    $tempConn->query('CREATE DATABASE "' . str_replace('"', '""', $dbName) . '"');
                }
            } else {
                $tempConn->query("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }

            return true;
        } catch (Exception $e) {
            return "Échec de création de la base : " . $e->getMessage();
        }
    }
}
