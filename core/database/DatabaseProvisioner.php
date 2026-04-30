<?php
namespace Core\Database;

use Core\Database\Drivers\PgsqlDriver;

class DatabaseProvisioner
{
    public function provisionPgsql(array $adminConfig, string $database, string $appUser, string $appPassword): void
    {
        $database = $this->cleanIdentifier($database);
        $appUser = $this->cleanIdentifier($appUser);

        $serverConfig = $adminConfig;
        $serverConfig['database'] = $serverConfig['maintenance_database'] ?? 'postgres';

        $server = new PgsqlDriver($serverConfig);

        $roleExists = $server
            ->query('SELECT 1 FROM pg_roles WHERE rolname = :role', ['role' => $appUser])
            ->fetch();

        $quotedUser = $this->quoteIdentifier($appUser);
        $quotedPassword = $server->getPdo()->quote($appPassword);

        if ($roleExists) {
            $server->query("ALTER ROLE {$quotedUser} WITH LOGIN PASSWORD {$quotedPassword}");
        } else {
            $server->query("CREATE ROLE {$quotedUser} WITH LOGIN PASSWORD {$quotedPassword}");
        }

        $dbExists = $server
            ->query('SELECT 1 FROM pg_database WHERE datname = :database', ['database' => $database])
            ->fetch();

        $quotedDatabase = $this->quoteIdentifier($database);

        if (!$dbExists) {
            $server->query("CREATE DATABASE {$quotedDatabase} OWNER {$quotedUser}");
        } else {
            $server->query("ALTER DATABASE {$quotedDatabase} OWNER TO {$quotedUser}");
        }

        $targetConfig = $adminConfig;
        $targetConfig['database'] = $database;
        $target = new PgsqlDriver($targetConfig);

        $target->query("GRANT CONNECT ON DATABASE {$quotedDatabase} TO {$quotedUser}");
        $target->query("GRANT USAGE, CREATE ON SCHEMA public TO {$quotedUser}");
        $target->query("GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO {$quotedUser}");
        $target->query("GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO {$quotedUser}");
        $target->query("ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON TABLES TO {$quotedUser}");
        $target->query("ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON SEQUENCES TO {$quotedUser}");
    }

    private function cleanIdentifier(string $value): string
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $value)) {
            throw new \InvalidArgumentException("Invalid PostgreSQL identifier: {$value}");
        }

        return $value;
    }

    private function quoteIdentifier(string $value): string
    {
        return '"' . str_replace('"', '""', $value) . '"';
    }
}
