<?php
namespace Core\Events;

class Event implements EventInterface
{
    protected string $name;
    protected array $params;
    protected bool $propagationStopped = false;

    public function __construct(string $name, array $params = [])
    {
        $this->name = $name;
        $this->params = $params;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function getParam(string $name, $default = null)
    {
        return $this->params[$name] ?? $default;
    }

    public function setParam(string $name, $value): void
    {
        $this->params[$name] = $value;
    }
    
    public function getParams(): array
    {
        return $this->params;
    }
}
