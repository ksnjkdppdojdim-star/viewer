<?php
namespace Core\Auth;

use Core\Database\Connection;
use Core\Http\Session;

class AuthManager
{
    public const SESSION_KEY = 'viewer_admin_user';

    public function attempt(array $dbConfig, string $email, string $password): bool
    {
        $db = Connection::make($dbConfig, 'system');
        $stmt = $db->query(
            "SELECT id, email, password_hash, global_role FROM users WHERE email = :email LIMIT 1",
            ['email' => $email]
        );
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        Session::start();
        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = [
            'id' => (int) $user['id'],
            'email' => $user['email'],
            'role' => $user['global_role'],
        ];

        return true;
    }

    public function loginAsAdmin(string $email): void
    {
        Session::start();
        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = [
            'id' => null,
            'email' => $email,
            'role' => 'admin',
        ];
    }

    public function check(): bool
    {
        Session::start();

        return !empty($_SESSION[self::SESSION_KEY]);
    }

    public function user(): ?array
    {
        Session::start();

        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    public function logout(): void
    {
        Session::start();
        unset($_SESSION[self::SESSION_KEY]);
        session_regenerate_id(true);
    }
}
