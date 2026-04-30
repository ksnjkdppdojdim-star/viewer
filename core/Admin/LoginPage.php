<?php
namespace Core\Admin;

use Core\Security\Csrf;

class LoginPage
{
    public function render(?string $error = null): void
    {
        echo '<!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
        echo '<title>Connexion | Viewer</title>';
        echo '<style>body{font-family:Arial,sans-serif;margin:0;min-height:100vh;display:grid;place-items:center;background:#f6f7f9;color:#1f2937}.panel{width:min(380px,calc(100vw - 32px));background:white;border:1px solid #e5e7eb;border-radius:8px;padding:24px}input{box-sizing:border-box;width:100%;padding:11px;margin:8px 0 14px;border:1px solid #d1d5db;border-radius:6px}.btn{width:100%;background:#2563eb;color:white;border:0;border-radius:6px;padding:11px;cursor:pointer}.error{background:#fee2e2;color:#991b1b;border-radius:6px;padding:10px}</style>';
        echo '</head><body><form class="panel" method="POST">';
        echo Csrf::field();
        echo '<h1>Connexion admin</h1>';
        if ($error) {
            echo '<p class="error">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</p>';
        }
        echo '<label>Email</label><input type="email" name="email" required>';
        echo '<label>Mot de passe</label><input type="password" name="password" required>';
        echo '<button class="btn" type="submit">Se connecter</button>';
        echo '</form></body></html>';
    }
}
