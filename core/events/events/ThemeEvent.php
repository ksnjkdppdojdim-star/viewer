<?php
namespace Core\Events\Events;

use Core\Events\Event;

class ThemeEvent extends Event
{
    const BEFORE_LOAD   = 'theme.before_load';
    const AFTER_LOAD    = 'theme.after_load';
    const SWITCH        = 'theme.switch';
    
    // Rendu
    const BEFORE_RENDER = 'theme.before_render';
    const AFTER_RENDER  = 'theme.after_render';
    const PARTIAL_RENDER = 'theme.partial.render';
    
    // Assets
    const ASSET_REGISTER = 'theme.asset.register';
    const ASSET_ENQUEUE  = 'theme.asset.enqueue';

    public function __construct(string $name, string $themeName, array $params = [])
    {
        parent::__construct($name, array_merge(['theme_name' => $themeName], $params));
    }

    public function getThemeName(): string
    {
        return $this->getParam('theme_name');
    }
}
