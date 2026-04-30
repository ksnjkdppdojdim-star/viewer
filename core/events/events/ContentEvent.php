<?php
namespace Core\Events\Events;

use Core\Events\Event;

class ContentEvent extends Event
{
    // CRUD classique
    const BEFORE_SAVE   = 'content.before_save';
    const AFTER_SAVE    = 'content.after_save';
    const BEFORE_DELETE = 'content.before_delete';
    const AFTER_DELETE  = 'content.after_delete';
    
    // Récupération
    const BEFORE_FETCH  = 'content.before_fetch';
    const AFTER_FETCH   = 'content.after_fetch';
    
    // Types de contenu
    const TYPE_CREATE   = 'content.type.create';
    const TYPE_UPDATE   = 'content.type.update';
    const TYPE_DELETE   = 'content.type.delete';
    
    // Médias
    const MEDIA_UPLOAD         = 'content.media.upload';
    const MEDIA_BEFORE_PROCESS = 'content.media.before_process';
    const MEDIA_DELETE         = 'content.media.delete';

    public function __construct(string $name, string $contentType, array $data = [], array $params = [])
    {
        parent::__construct($name, array_merge([
            'content_type' => $contentType,
            'data' => $data
        ], $params));
    }

    public function getContentType(): string
    {
        return $this->getParam('content_type');
    }

    public function getData(): array
    {
        return $this->getParam('data');
    }

    public function setData(array $data): void
    {
        $this->setParam('data', $data);
    }
}
