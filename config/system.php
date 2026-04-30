<?php
// config/system.php

use Core\Support\Env;

return [
    'env' => Env::get('APP_ENV', 'development'),
    'debug' => Env::get('APP_DEBUG', true),

    'paths' => [
        'core'          => dirname(__DIR__) . '/core',
        'themes'        => dirname(__DIR__) . '/themes',
        'plugins'       => dirname(__DIR__) . '/plugins',
        'overrides'     => dirname(__DIR__) . '/overrides',
        'organizations' => dirname(__DIR__) . '/organizations',
        'storage'       => dirname(__DIR__) . '/storage',
        'public'        => dirname(__DIR__) . '/public',
        'migrations'    => dirname(__DIR__) . '/migrations',
    ],

    'cache' => [
        'templates' => dirname(__DIR__) . '/storage/cache/templates',
        'queries'   => dirname(__DIR__) . '/storage/cache/queries',
    ],
];
