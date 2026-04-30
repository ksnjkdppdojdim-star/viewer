<?php
// public/index.php

define('VIEWER_ROOT', dirname(__DIR__));

require VIEWER_ROOT . '/core/bootstrap.php';

$sysConfig = require VIEWER_ROOT . '/config/system.php';
$dbConfig = require VIEWER_ROOT . '/config/database.php';
$viewerConfig = require VIEWER_ROOT . '/config/viewer.php';

if ($sysConfig['debug']) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
}

try {
    $request = new \Core\Http\Request();
    $router  = new \Core\Http\Router();
    $auth = new \Core\Auth\AuthManager();

    $isInstalled = file_exists(VIEWER_ROOT . '/config/installed.txt');
    $uri = $request->getUri();

    if (!$isInstalled && strpos($uri, '/install') !== 0) {
        $router->redirect('/install');
    }

    $router->add('GET', '/install', function($req) {
        (new \Core\Install\InstallerController())->handle($req);
    });
    $router->add('POST', '/install', function($req) {
        (new \Core\Install\InstallerController())->handle($req);
    });

    $router->add('GET', '/login', function($req) {
        (new \Core\Admin\LoginPage())->render();
    });
    $router->add('POST', '/login', function($req) use ($dbConfig) {
        if (!\Core\Security\Csrf::validate($req->post('_csrf_token'))) {
            header('HTTP/1.1 419 Authentication Timeout');
            (new \Core\Admin\LoginPage())->render('Session expiree. Rechargez la page.');
            return;
        }

        $auth = new \Core\Auth\AuthManager();
        if ($auth->attempt($dbConfig['system'], (string) $req->post('email'), (string) $req->post('password'))) {
            header('Location: /admin');
            exit;
        }

        header('HTTP/1.1 401 Unauthorized');
        (new \Core\Admin\LoginPage())->render('Identifiants invalides.');
    });
    $router->add('POST', '/logout', function($req) {
        if (\Core\Security\Csrf::validate($req->post('_csrf_token'))) {
            (new \Core\Auth\AuthManager())->logout();
        }

        header('Location: /login');
        exit;
    });

    $router->add('GET', '/admin', function($req) use ($auth, $dbConfig) {
        (new \Core\Admin\AdminDashboard())->render($dbConfig['system'], $auth);
    });

    $router->add('GET', '/', function($req) {
        echo "<h1>Bienvenue sur Viewer</h1><p>Le site public est en ligne.</p>";
    });

    $router->dispatch($request);
} catch (\Exception $e) {
    if ($sysConfig['debug']) {
        echo "<h1>System Error</h1>";
        echo "<pre>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</pre>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . "</pre>";
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo "<h1>500 Internal Server Error</h1>";
    }
}
