<?php
namespace Core\Events\Events;

use Core\Events\Event;

class PageEvent extends Event
{
    const BEFORE_RENDER = 'page.before_render';
    const AFTER_RENDER = 'page.after_render';

    public function __construct(string $name, string $pageTitle, string $content = '')
    {
        parent::__construct($name, [
            'title' => $pageTitle,
            'content' => $content
        ]);
    }

    public function getTitle(): string
    {
        return $this->getParam('title');
    }

    public function setTitle(string $title): void
    {
        $this->setParam('title', $title);
    }
    
    public function getContent(): string
    {
        return $this->getParam('content');
    }
    
    public function setContent(string $content): void
    {
        $this->setParam('content', $content);
    }
}
