<?php
namespace Core\Engine;

class DtvRenderer
{
    private DtvCache $cache;
    private \Core\Overrides\OverrideResolver $resolver;
    private string $activeTheme;

    public function __construct(DtvCache $cache, \Core\Overrides\OverrideResolver $resolver, string $activeTheme)
    {
        $this->cache = $cache;
        $this->resolver = $resolver;
        $this->activeTheme = $activeTheme;
    }

    /**
     * Rendu principal d'une vue DTV
     */
    public function render(string $viewName, array $data = []): string
    {
        $viewPath = $this->resolveViewPath($viewName);
        
        if (!$viewPath) {
            throw new \Exception("View [{$viewName}] not found in theme [{$this->activeTheme}] or its overrides.");
        }

        $compiledPath = $this->cache->getCompiledPath($viewPath);

        extract($data);

        ob_start();
        require $compiledPath;
        return ob_get_clean();
    }

    public function renderPartial(string $viewName, array $data = []): void
    {
        echo $this->render($viewName, $data);
    }

    /**
     * Trouve le fichier .dtv en utilisant le resolver
     */
    private function resolveViewPath(string $viewName): ?string
    {
        $relativePath = str_replace('.', '/', $viewName) . '.dtv';
        return $this->resolver->resolveThemeView($this->activeTheme, $relativePath);
    }
}
