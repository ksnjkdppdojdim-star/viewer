<?php
namespace Core\Admin;

use Core\Auth\AuthManager;
use Core\Database\Connection;
use Core\Security\Csrf;

class AdminDashboard
{
    public function render(array $dbConfig, AuthManager $auth): void
    {
        if (!$auth->check()) {
            header('Location: /login');
            exit;
        }

        $user = $auth->user();
        $stats = $this->stats($dbConfig);

        echo '<!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
        echo '<title>Viewer Admin</title>';
        echo '<style>body{font-family:Arial,sans-serif;margin:0;background:#f6f7f9;color:#1f2937}.top{background:#111827;color:white;padding:16px 24px;display:flex;justify-content:space-between;align-items:center}.wrap{max-width:1040px;margin:32px auto;padding:0 20px}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px}.card{background:white;border:1px solid #e5e7eb;border-radius:8px;padding:18px}.muted{color:#6b7280}.btn{background:#2563eb;color:white;border:0;border-radius:6px;padding:9px 12px;cursor:pointer}</style>';
        echo '</head><body>';
        echo '<div class="top"><strong>Viewer Admin</strong><form method="POST" action="/logout">' . Csrf::field() . '<button class="btn" type="submit">Deconnexion</button></form></div>';
        echo '<main class="wrap">';
        echo '<h1>Tableau de bord</h1>';
        echo '<p class="muted">Connecte comme ' . htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') . '.</p>';
        echo '<section class="grid">';
        $this->card('Organisations', (string) $stats['organizations']);
        $this->card('Sites', (string) $stats['sites']);
        $this->card('Plugins actifs', (string) $stats['plugins']);
        $this->card('Base systeme', htmlspecialchars($dbConfig['database'] ?? 'unknown', ENT_QUOTES, 'UTF-8'));
        echo '</section>';
        echo '</main></body></html>';
    }

    private function card(string $label, string $value): void
    {
        echo '<article class="card"><div class="muted">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</div>';
        echo '<strong style="font-size:28px">' . $value . '</strong></article>';
    }

    private function stats(array $dbConfig): array
    {
        $db = Connection::make($dbConfig, 'system');

        return [
            'organizations' => $this->count($db, 'organizations'),
            'sites' => $this->count($db, 'sites'),
            'plugins' => $this->count($db, 'site_plugins'),
        ];
    }

    private function count($db, string $table): int
    {
        $stmt = $db->query("SELECT COUNT(*) AS count FROM {$table}");
        $row = $stmt->fetch();

        return (int) ($row['count'] ?? 0);
    }
}
