<?php

define('VIEWER_ROOT', dirname(__DIR__, 2));
require VIEWER_ROOT . '/core/bootstrap.php';

$sysConfig = require VIEWER_ROOT . '/config/system.php';

use Core\Engine\DtvCache;
use Core\Engine\DtvCompiler;
use Core\Engine\DtvRenderer;
use Core\Overrides\OverrideResolver;

echo "<pre>";
echo "Testing DTV Engine...\n\n";

$compiler = new DtvCompiler();
$cache = new DtvCache($sysConfig['cache']['templates'], $compiler);
$resolver = new OverrideResolver(VIEWER_ROOT);
$renderer = new DtvRenderer($cache, $resolver, 'test-theme');

$data = [
    'siteName' => 'Viewer Test',
    'description' => 'Ceci est un test <em>avec HTML non échappé</em>.',
    'features' => [
        ['name' => 'Variables', 'status' => 'active'],
        ['name' => 'Boucles', 'status' => 'active'],
        ['name' => 'Conditions', 'status' => 'active'],
        ['name' => 'Inclusions', 'status' => 'active'],
        ['name' => 'Téléportation', 'status' => 'inactive'],
    ],
];

try {
    $html = $renderer->render('test', $data);
    echo "=== Rendu Final ===\n\n";
    echo htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
} catch (\Exception $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}

echo "</pre>";
