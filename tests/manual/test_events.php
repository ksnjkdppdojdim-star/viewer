<?php

define('VIEWER_ROOT', dirname(__DIR__, 2));
require VIEWER_ROOT . '/core/bootstrap.php';

use Core\Events\EventManager;
use Core\Events\Events\PageEvent;

echo "<pre>";
echo "Testing Object-Oriented EventManager...\n\n";

$eventManager = new EventManager();

$eventManager->attach(PageEvent::BEFORE_RENDER, function(PageEvent $event) {
    echo "-> Listener priority 10\n";
    $event->setTitle($event->getTitle() . " - Modifié par Plugin 1");
}, 10);

$eventManager->attach(PageEvent::BEFORE_RENDER, function(PageEvent $event) {
    echo "-> Listener priority 100\n";
    $event->setTitle("[PREFIX] " . $event->getTitle());
}, 100);

$eventManager->attach(PageEvent::BEFORE_RENDER, function(PageEvent $event) {
    echo "-> Listener priority 50 stops propagation\n";
    $event->stopPropagation();
}, 50);

$eventManager->attach(PageEvent::BEFORE_RENDER, function(PageEvent $event) {
    echo "-> Listener priority 150 throws\n";
    throw new \Exception("Crash test !");
}, 150);

$pageEvent = new PageEvent(PageEvent::BEFORE_RENDER, "Accueil");
$eventManager->trigger($pageEvent);

echo "\nTitre final de la page : " . htmlspecialchars($pageEvent->getTitle(), ENT_QUOTES, 'UTF-8') . "\n";
echo "</pre>";
