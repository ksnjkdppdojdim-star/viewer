<?php
namespace Core\Engine;

class DtvCache
{
    private string $cachePath;
    private DtvCompiler $compiler;

    public function __construct(string $cachePath, DtvCompiler $compiler)
    {
        $this->cachePath = rtrim($cachePath, '/');
        $this->compiler = $compiler;

        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }

    /**
     * Récupère le chemin du fichier compilé.
     * Le compile s'il n'existe pas ou s'il est obsolète.
     */
    public function getCompiledPath(string $viewPath): string
    {
        if (!file_exists($viewPath)) {
            throw new \Exception("View file not found: {$viewPath}");
        }

        // Crée un hash basé sur le chemin absolu du fichier original
        $viewHash = md5($viewPath);
        $cachedFile = $this->cachePath . '/' . $viewHash . '.php';

        // Vérifie si on doit compiler
        if ($this->needsCompilation($viewPath, $cachedFile)) {
            $this->compileAndSave($viewPath, $cachedFile);
        }

        return $cachedFile;
    }

    protected function needsCompilation(string $viewPath, string $cachedFile): bool
    {
        // Si le cache n'existe pas
        if (!file_exists($cachedFile)) {
            return true;
        }

        // Si le fichier source a été modifié plus récemment que le cache
        if (filemtime($viewPath) > filemtime($cachedFile)) {
            return true;
        }

        return false;
    }

    protected function compileAndSave(string $viewPath, string $cachedFile): void
    {
        $content = file_get_contents($viewPath);
        $compiled = $this->compiler->compile($content);
        
        // Ajout d'un petit commentaire généré en haut du cache pour debug
        $header = "<?php\n/**\n * Compiled DTV Template\n * Source: {$viewPath}\n */\n?>";
        
        file_put_contents($cachedFile, $header . $compiled);
    }
}
