<?php
// config/database.php

use Core\Support\Env;

return [
    'system' => [
        'driver'   => Env::get('DB_DRIVER', 'mysql'),
        'host'     => Env::get('DB_HOST', '127.0.0.1'),
        'port'     => Env::get('DB_PORT', 3306),
        'database' => Env::get('DB_DATABASE', 'viewer_system'),
        'username' => Env::get('DB_USERNAME', 'root'),
        'password' => Env::get('DB_PASSWORD', ''),
        'charset'  => Env::get('DB_CHARSET', 'utf8mb4'),
    ],

    'admin' => [
        'driver'   => Env::get('DB_DRIVER', 'mysql'),
        'host'     => Env::get('DB_HOST', '127.0.0.1'),
        'port'     => Env::get('DB_PORT', 3306),
        'database' => Env::get('DB_ADMIN_DATABASE', 'postgres'),
        'username' => Env::get('DB_ADMIN_USERNAME', Env::get('DB_USERNAME', 'root')),
        'password' => Env::get('DB_ADMIN_PASSWORD', Env::get('DB_PASSWORD', '')),
        'charset'  => Env::get('DB_CHARSET', 'utf8mb4'),
    ],

    'client_default' => [
        'driver'   => Env::get('CLIENT_DB_DRIVER', 'pgsql'),
        'host'     => Env::get('CLIENT_DB_HOST', '127.0.0.1'),
        'port'     => Env::get('CLIENT_DB_PORT', 5432),
        'username' => Env::get('CLIENT_DB_USERNAME', 'postgres'),
        'password' => Env::get('CLIENT_DB_PASSWORD', ''),
        'charset'  => Env::get('CLIENT_DB_CHARSET', 'utf8'),
    ],
];
