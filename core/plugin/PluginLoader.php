<?php
namespace Core\Plugin;

use Core\Events\EventManager;
use Exception;

class PluginLoader
{
    private string $pluginsPath;
    private EventManager $eventManager;
    private array $loadedPlugins = [];

    public function __construct(string $pluginsPath, EventManager $eventManager)
    {
        $this->pluginsPath = rtrim($pluginsPath, '/');
        $this->eventManager = $eventManager;
    }

    /**
     * Charge une liste de plugins actifs.
     */
    public function loadPlugins(array $pluginNames): void
    {
        foreach ($pluginNames as $name) {
            $this->loadPlugin($name);
        }
    }

    /**
     * Charge un plugin spécifique.
     */
    public function loadPlugin(string $name): void
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $name)) {
            error_log("Plugin [{$name}] ignored: invalid plugin name.");
            return;
        }

        $pluginDir = "{$this->pluginsPath}/{$name}";
        $configPath = "{$pluginDir}/plugin.json";

        if (!file_exists($configPath)) {
            error_log("Plugin [{$name}] ignored: plugin.json missing.");
            return;
        }

        $config = json_decode(file_get_contents($configPath), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($config)) {
            error_log("Plugin [{$name}] ignored: invalid plugin.json.");
            return;
        }
        
        // 1. Charger le fichier de bootstrap s'il existe
        $bootstrapFile = "{$pluginDir}/bootstrap.php";
        if (file_exists($bootstrapFile)) {
            // On passe l'EventManager au scope du fichier bootstrap
            $eventManager = $this->eventManager;
            require_once $bootstrapFile;
        }

        $this->loadedPlugins[$name] = $config;
    }

    public function getLoadedPlugins(): array
    {
        return $this->loadedPlugins;
    }
}
