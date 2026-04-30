<?php
namespace Core\Events\Events;

use Core\Events\Event;

class SystemEvent extends Event
{
    // Cycle de vie global
    const BOOTSTRAP_START = 'system.bootstrap.start';
    const BOOTSTRAP_DONE  = 'system.bootstrap.done';
    const SHUTDOWN        = 'system.shutdown';
    
    // Routage
    const ROUTING_START   = 'system.routing.start';
    const ROUTING_MATCH   = 'system.routing.match'; // Quand une route est trouvée
    const ROUTING_END     = 'system.routing.end';
    
    // Erreurs
    const ERROR           = 'system.error';
    const EXCEPTION       = 'system.exception';
    const ERROR_404       = 'system.error.404';
    const ERROR_403       = 'system.error.403';

    // Requête / Réponse
    const REQUEST_RECEIVED = 'system.request.received';
    const RESPONSE_SENDING  = 'system.response.sending';
    const RESPONSE_SENT     = 'system.response.sent';

    public function __construct(string $name, array $params = [])
    {
        parent::__construct($name, $params);
    }
}
