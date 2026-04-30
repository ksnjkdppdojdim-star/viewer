<?php
namespace Core\Events\Events;

use Core\Events\Event;

class PluginEvent extends Event
{
    const BEFORE_LOAD = 'plugin.before_load';
    const AFTER_LOAD  = 'plugin.after_load';
    
    const INSTALL     = 'plugin.install';
    const UNINSTALL   = 'plugin.uninstall';
    const ACTIVATE    = 'plugin.activate';
    const DEACTIVATE  = 'plugin.deactivate';
    
    const SETTINGS_UPDATE = 'plugin.settings_update';

    public function __construct(string $name, string $pluginName, array $params = [])
    {
        parent::__construct($name, array_merge(['plugin_name' => $pluginName], $params));
    }

    public function getPluginName(): string
    {
        return $this->getParam('plugin_name');
    }
}
