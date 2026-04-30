<?php
namespace Core\Theme;

use Core\Overrides\OverrideResolver;
use Exception;

class ThemeLoader
{
    private string $themesPath;
    private OverrideResolver $resolver;
    private array $activeThemeData = [];

    public function __construct(string $themesPath, OverrideResolver $resolver)
    {
        $this->themesPath = rtrim($themesPath, '/');
        $this->resolver = $resolver;
    }

    /**
     * Charge les métadonnées du thème actif.
     */
    public function load(string $themeName): array
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $themeName)) {
            throw new Exception("Invalid theme name [{$themeName}]");
        }

        $configPath = "{$this->themesPath}/{$themeName}/theme.json";
        
        if (!file_exists($configPath)) {
            throw new Exception("Config file not found for theme [{$themeName}] at {$configPath}");
        }

        $config = json_decode(file_get_contents($configPath), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON in theme.json for theme [{$themeName}]");
        }

        $this->activeThemeData = $config;
        
        // On pourrait ici enregistrer d'autres choses, 
        // mais le OverrideResolver gère déjà la priorité dynamiquement.
        
        return $config;
    }

    public function getActiveThemeData(): array
    {
        return $this->activeThemeData;
    }
}
