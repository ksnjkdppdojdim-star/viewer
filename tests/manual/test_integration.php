<?php

define('VIEWER_ROOT', dirname(__DIR__, 2));
require VIEWER_ROOT . '/core/bootstrap.php';

$sysConfig = require VIEWER_ROOT . '/config/system.php';

use Core\Engine\DtvCache;
use Core\Engine\DtvCompiler;
use Core\Engine\DtvRenderer;
use Core\Events\EventManager;
use Core\Events\Events\PageEvent;
use Core\Overrides\OverrideResolver;
use Core\Plugin\PluginLoader;
use Core\Theme\ThemeLoader;

echo "<pre>";
echo "Testing Full Integration (Theme + Plugin + Events + DTV)...\n\n";

$eventManager = new EventManager();
$resolver = new OverrideResolver(VIEWER_ROOT);
$compiler = new DtvCompiler();
$cache = new DtvCache($sysConfig['cache']['templates'], $compiler);

$themeLoader = new ThemeLoader(VIEWER_ROOT . '/themes', $resolver);
$themeConfig = $themeLoader->load('test-theme');
echo "Theme Loaded: " . htmlspecialchars($themeConfig['name'], ENT_QUOTES, 'UTF-8') . "\n";

$pluginLoader = new PluginLoader(VIEWER_ROOT . '/plugins', $eventManager);
$pluginLoader->loadPlugin('demo-plugin');
echo "Plugin Loaded: demo-plugin\n\n";

$renderer = new DtvRenderer($cache, $resolver, 'test-theme');

$event = new PageEvent(PageEvent::BEFORE_RENDER, "Ma Super Page", "Contenu original de la page.");
$eventManager->trigger($event);

$html = $renderer->render('test', [
    'siteName' => $event->getTitle(),
    'description' => $event->getContent(),
    'features' => [
        ['name' => 'Intégration Loaders', 'status' => 'active'],
        ['name' => 'Plugins Auto-Bootstrap', 'status' => 'active'],
    ],
]);

echo "=== Rendu Final ===\n\n";
echo htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
echo "</pre>";
