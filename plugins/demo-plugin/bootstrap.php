<?php
/**
 * plugins/demo-plugin/bootstrap.php
 */

use Core\Events\Events\PageEvent;

// On s'abonne à l'événement de rendu de page
$eventManager->attach(PageEvent::BEFORE_RENDER, function(PageEvent $event) {
    // On ajoute un petit message au titre
    $event->setTitle($event->getTitle() . " [Propulsé par Demo Plugin]");
    
    // On peut aussi modifier le contenu
    $content = $event->getContent();
    $event->setContent($content . "<p style='color: green;'>Ce message a été ajouté par un plugin via le EventManager !</p>");
});
