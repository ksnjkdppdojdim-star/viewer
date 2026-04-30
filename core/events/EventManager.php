<?php
namespace Core\Events;

class EventManager
{
    /**
     * Tableau des listeners : $listeners[$eventName][$priority][] = callable
     */
    protected array $listeners = [];

    /**
     * Attache un listener à un événement avec une priorité.
     * Priorité haute = s'exécute en premier.
     */
    public function attach(string $eventName, callable $listener, int $priority = 10): void
    {
        $this->listeners[$eventName][$priority][] = $listener;
    }

    /**
     * Déclenche un événement.
     */
    public function trigger(EventInterface $event): EventInterface
    {
        $eventName = $event->getName();

        if (empty($this->listeners[$eventName])) {
            return $event; // Aucun listener, on retourne l'événement intact
        }

        // On copie les listeners pour cet événement
        $priorities = $this->listeners[$eventName];
        
        // Tri décroissant par priorité (ex: 100 s'exécute avant 10)
        krsort($priorities);

        foreach ($priorities as $priority => $listenersList) {
            foreach ($listenersList as $listener) {
                // Si la propagation a été stoppée par un listener précédent, on quitte.
                if ($event->isPropagationStopped()) {
                    return $event;
                }

                try {
                    // Exécution du listener
                    call_user_func($listener, $event, $this);
                } catch (\Exception $e) {
                    // Gestion d'erreur (logging, etc.) pour ne pas crasher tout le CMS
                    // TODO: Intégrer le système de log global
                    error_log("Event {$eventName} listener exception: " . $e->getMessage());
                }
            }
        }

        return $event;
    }
}
