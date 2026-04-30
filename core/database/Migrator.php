<?php
namespace Core\Database;

use Core\Database\Drivers\DriverInterface;

class Migrator
{
    private DriverInterface $db;

    public function __construct(DriverInterface $db)
    {
        $this->db = $db;
    }

    public function run(string $type = 'system')
    {
        $migrationsPath = VIEWER_ROOT . '/migrations/' . $type;
        
        if (!is_dir($migrationsPath)) {
            throw new \Exception("Migrations directory not found: {$migrationsPath}");
        }

        $this->createMigrationsTable();

        $driverExt = ($this->db instanceof \Core\Database\Drivers\PgsqlDriver) ? 'pgsql' : 'mysql';
        $files = glob($migrationsPath . '/*.' . $driverExt . '.sql');
        
        if (empty($files)) {
            echo "No migrations found for driver: {$driverExt}\n";
            return;
        }

        sort($files);

        foreach ($files as $file) {
            $filename = basename($file);
            if (!$this->hasRun($filename)) {
                echo "Running {$filename}...\n";
                $sql = file_get_contents($file);
                
                try {
                    foreach ($this->splitSqlStatements($sql) as $statement) {
                        $this->db->query($statement);
                    }
                    $this->logMigration($filename);
                    echo "-> Success\n";
                } catch (\Exception $e) {
                    echo "-> Error in {$filename}: " . $e->getMessage() . "\n";
                    throw $e;
                }
            }
        }
    }

    private function createMigrationsTable()
    {
        $isPgsql = $this->db instanceof \Core\Database\Drivers\PgsqlDriver;
        $idType = $isPgsql ? 'SERIAL PRIMARY KEY' : 'INT AUTO_INCREMENT PRIMARY KEY';

        $sql = "
            CREATE TABLE IF NOT EXISTS migrations (
                id {$idType},
                migration VARCHAR(255) NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        $this->db->query($sql);
    }

    private function hasRun(string $migration): bool
    {
        $sql = "SELECT COUNT(*) as count FROM migrations WHERE migration = :migration";
        $stmt = $this->db->query($sql, ['migration' => $migration]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    private function logMigration(string $migration)
    {
        $sql = "INSERT INTO migrations (migration) VALUES (:migration)";
        $this->db->query($sql, ['migration' => $migration]);
    }

    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $current = '';
        $length = strlen($sql);
        $quote = null;
        $lineComment = false;
        $blockComment = false;

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            $next = $sql[$i + 1] ?? '';

            if ($lineComment) {
                if ($char === "\n") {
                    $lineComment = false;
                }
                $current .= $char;
                continue;
            }

            if ($blockComment) {
                if ($char === '*' && $next === '/') {
                    $blockComment = false;
                    $current .= '*/';
                    $i++;
                    continue;
                }
                $current .= $char;
                continue;
            }

            if ($quote !== null) {
                $current .= $char;
                if ($char === $quote && ($i === 0 || $sql[$i - 1] !== '\\')) {
                    $quote = null;
                }
                continue;
            }

            if ($char === '-' && $next === '-') {
                $lineComment = true;
                $current .= '--';
                $i++;
                continue;
            }

            if ($char === '/' && $next === '*') {
                $blockComment = true;
                $current .= '/*';
                $i++;
                continue;
            }

            if ($char === "'" || $char === '"') {
                $quote = $char;
                $current .= $char;
                continue;
            }

            if ($char === ';') {
                $statement = trim($current);
                if ($statement !== '') {
                    $statements[] = $statement;
                }
                $current = '';
                continue;
            }

            $current .= $char;
        }

        $statement = trim($current);
        if ($statement !== '') {
            $statements[] = $statement;
        }

        return $statements;
    }
}
