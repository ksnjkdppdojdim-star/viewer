<?php
namespace Core\Install;

use Core\Auth\AuthManager;
use Core\Http\Session;
use Core\Security\Csrf;

class InstallerController
{
    private Installer $installer;

    public function __construct()
    {
        $this->installer = new Installer();
    }

    public function handle($request): void
    {
        Session::start();
        $step = $_SESSION['install_step'] ?? 1;
        $error = null;

        if ($request->isPost()) {
            if (!Csrf::validate($request->post('_csrf_token'))) {
                $error = "Session expirée. Rechargez la page puis réessayez.";
            }

            $action = $error ? null : $request->post('action');

            if ($action === 'step1') {
                $_SESSION['install_step'] = 2;
                header("Location: /install");
                exit;
            }

            if ($action === 'step2') {
                $dbConfig = [
                    'driver'   => $request->post('driver'),
                    'host'     => $request->post('host'),
                    'database' => $request->post('database'),
                    'username' => $request->post('username'),
                    'password' => $request->post('password'),
                    'charset'  => 'utf8mb4',
                ];

                $testResult = $this->installer->testConnection($dbConfig);
                if ($testResult === true) {
                    $_SESSION['temp_db_config'] = $dbConfig;

                    try {
                        $this->installer->runMigrations($dbConfig);
                        $_SESSION['install_step'] = 3;
                        header("Location: /install");
                        exit;
                    } catch (\Exception $e) {
                        $error = "Erreur de migration : " . $e->getMessage();
                    }
                } else {
                    $error = "Erreur de connexion : " . $testResult;
                }
            }

            if ($action === 'step3') {
                $email = trim((string) $request->post('email'));
                $pass = (string) $request->post('password');
                $dbConfig = $_SESSION['temp_db_config'] ?? null;

                if (!$dbConfig) {
                    $error = "Configuration d'installation introuvable. Reprenez depuis l'étape base de données.";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Email administrateur invalide.";
                } elseif (strlen($pass) < 8) {
                    $error = "Le mot de passe administrateur doit contenir au moins 8 caractères.";
                }

                try {
                    if (!$error) {
                        $this->installer->createAdmin($dbConfig, $email, $pass);
                        $this->saveEnvConfig($dbConfig);
                        $this->installer->finalize();
                        (new AuthManager())->loginAsAdmin($email);
                        unset($_SESSION['install_step'], $_SESSION['temp_db_config']);
                        header("Location: /admin");
                        exit;
                    }
                } catch (\Exception $e) {
                    $error = "Finalization error: " . $e->getMessage();
                }
            }
        }

        $requirements = $this->installer->checkRequirements();
        $debugInfo = $this->installer->getDebugInfo();

        $allOk = true;
        if (!$requirements['php_version']) {
            $allOk = false;
        }

        foreach ($requirements['extensions'] as $ext => $loaded) {
            if ($loaded) {
                continue;
            }

            if (($ext === 'PDO MySQL' || $ext === 'PDO PostgreSQL') && (extension_loaded('pdo_mysql') || extension_loaded('pdo_pgsql'))) {
                continue;
            }

            if ($ext !== 'PDO MySQL' && $ext !== 'PDO PostgreSQL') {
                $allOk = false;
            }
        }

        foreach ($requirements['permissions'] as $writable) {
            if (!$writable) {
                $allOk = false;
            }
        }

        if (!$allOk && $step > 1) {
            $_SESSION['install_step'] = 1;
            $step = 1;
        }

        require VIEWER_ROOT . '/core/install/views/install.php';
    }

    private function saveEnvConfig(array $config): void
    {
        $driver = $config['driver'] ?? 'mysql';
        $values = [
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'DB_DRIVER' => $driver,
            'DB_HOST' => $config['host'] ?? '127.0.0.1',
            'DB_PORT' => $config['port'] ?? ($driver === 'pgsql' ? 5432 : 3306),
            'DB_DATABASE' => $config['database'] ?? '',
            'DB_USERNAME' => $config['username'] ?? '',
            'DB_PASSWORD' => $config['password'] ?? '',
            'DB_CHARSET' => $config['charset'] ?? 'utf8mb4',
        ];

        $lines = [];
        foreach ($values as $key => $value) {
            $escaped = str_replace(["\\", "\n", "\r", '"'], ["\\\\", '', '', '\"'], (string) $value);
            $lines[] = $key . '="' . $escaped . '"';
        }

        file_put_contents(VIEWER_ROOT . '/.env', implode(PHP_EOL, $lines) . PHP_EOL, LOCK_EX);
    }
}
