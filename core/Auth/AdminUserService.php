<?php
namespace Core\Auth;

use Core\Database\Connection;

class AdminUserService
{
    public function createOrUpdate(array $dbConfig, string $email, string $password, string $role = 'admin'): int
    {
        $email = trim(strtolower($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid admin email.');
        }

        if (strlen($password) < 8) {
            throw new \InvalidArgumentException('Admin password must contain at least 8 characters.');
        }

        $db = Connection::make($dbConfig, 'system');
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $existing = $db
            ->query('SELECT id FROM users WHERE email = :email LIMIT 1', ['email' => $email])
            ->fetch();

        if ($existing) {
            $db->query(
                'UPDATE users SET password_hash = :hash, global_role = :role WHERE id = :id',
                ['hash' => $hash, 'role' => $role, 'id' => $existing['id']]
            );

            return (int) $existing['id'];
        }

        $db->query(
            "INSERT INTO users (email, password_hash, global_role) VALUES (:email, :hash, :role)",
            ['email' => $email, 'hash' => $hash, 'role' => $role]
        );

        $stmt = $db->query('SELECT id FROM users WHERE email = :email LIMIT 1', ['email' => $email]);
        $created = $stmt->fetch();

        return (int) ($created['id'] ?? 0);
    }
}
