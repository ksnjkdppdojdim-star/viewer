<?php
namespace Core\Engine;

class DtvCompiler
{
    /**
     * Compile le contenu d'un fichier .dtv en code PHP natif.
     */
    public function compile(string $content): string
    {
        $content = $this->compileEcho($content);
        $content = $this->compileControlStructures($content);
        $content = $this->compileIncludes($content);
        
        return $content;
    }

    /**
     * Compile {{ var }} en <?php echo htmlspecialchars(var); ?>
     * et {!! var !!} en <?php echo var; ?> (non échappé)
     */
    protected function compileEcho(string $content): string
    {
        // Non échappé : {!! $var !!}
        $content = preg_replace('/\{!!\s*(.+?)\s*!!\}/s', '<?php echo $1; ?>', $content);
        
        // Échappé : {{ $var }}
        $content = preg_replace('/\{\{\s*(.+?)\s*\}\}/s', '<?php echo htmlspecialchars($1 ?? \'\', ENT_QUOTES, \'UTF-8\'); ?>', $content);
        
        return $content;
    }

    /**
     * Compile @if, @foreach, etc.
     */
    protected function compileControlStructures(string $content): string
    {
        // @if($condition)
        $content = preg_replace('/@if\s*\((.*)\)/', '<?php if ($1): ?>', $content);
        // @elseif($condition)
        $content = preg_replace('/@elseif\s*\((.*)\)/', '<?php elseif ($1): ?>', $content);
        // @else
        $content = preg_replace('/@else/', '<?php else: ?>', $content);
        // @endif
        $content = preg_replace('/@endif/', '<?php endif; ?>', $content);

        // @foreach($items as $item)
        $content = preg_replace('/@foreach\s*\((.*)\)/', '<?php foreach ($1): ?>', $content);
        // @endforeach
        $content = preg_replace('/@endforeach/', '<?php endforeach; ?>', $content);

        return $content;
    }

    /**
     * Compile @include('partial')
     */
    protected function compileIncludes(string $content): string
    {
        // Transforme @include('view_name') en un appel à notre Renderer
        // On suppose que la fonction includeDtv sera disponible dans le scope (gérée par le Renderer)
        $content = preg_replace('/@include\s*\((.*?)\)/', '<?php $this->renderPartial($1); ?>', $content);
        
        return $content;
    }
}
