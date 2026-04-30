<?php
namespace Core\Overrides;

class OverrideResolver
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * Résout le chemin d'une vue de thème.
     * Cherche d'abord dans overrides/, puis dans le thème original.
     */
    public function resolveThemeView(string $themeName, string $viewPath): ?string
    {
        // $viewPath est attendu sous forme "partials/header.dtv" ou "home.dtv"
        $themeName = $this->cleanSegment($themeName);
        $viewPath = $this->cleanViewPath($viewPath);
        
        $pathsToTry = [
            // 1. La surcharge
            $this->basePath . "/overrides/themes/{$themeName}/views/{$viewPath}",
            // 2. L'original
            $this->basePath . "/themes/{$themeName}/views/{$viewPath}",
        ];

        foreach ($pathsToTry as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Résout le chemin d'une vue de plugin.
     */
    public function resolvePluginView(string $pluginName, string $viewPath): ?string
    {
        $pluginName = $this->cleanSegment($pluginName);
        $viewPath = $this->cleanViewPath($viewPath);

        $pathsToTry = [
            // 1. La surcharge
            $this->basePath . "/overrides/plugins/{$pluginName}/views/{$viewPath}",
            // 2. L'original
            $this->basePath . "/plugins/{$pluginName}/views/{$viewPath}",
        ];

        foreach ($pathsToTry as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function cleanSegment(string $value): string
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $value)) {
            throw new \InvalidArgumentException("Invalid path segment: {$value}");
        }

        return $value;
    }

    private function cleanViewPath(string $value): string
    {
        $value = str_replace('\\', '/', $value);

        if (
            str_starts_with($value, '/') ||
            str_contains($value, '../') ||
            str_contains($value, '/..') ||
            !str_ends_with($value, '.dtv')
        ) {
            throw new \InvalidArgumentException("Invalid view path: {$value}");
        }

        return $value;
    }
}
