<?php

if (!defined('VIEWER_ROOT')) {
    define('VIEWER_ROOT', dirname(__DIR__));
}

spl_autoload_register(function ($class) {
    $prefix = 'Core\\';
    $baseDir = VIEWER_ROOT . '/core/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

\Core\Support\Env::load(VIEWER_ROOT . '/.env');
