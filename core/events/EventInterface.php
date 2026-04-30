<?php
namespace Core\Events;

interface EventInterface
{
    /**
     * Retourne le nom de l'événement.
     */
    public function getName(): string;

    /**
     * Stoppe la propagation vers les listeners suivants.
     */
    public function stopPropagation(): void;

    /**
     * Vérifie si la propagation a été stoppée.
     */
    public function isPropagationStopped(): bool;
    
    /**
     * Retourne un paramètre de l'événement.
     */
    public function getParam(string $name, $default = null);
    
    /**
     * Modifie ou ajoute un paramètre à l'événement.
     */
    public function setParam(string $name, $value): void;
}
