<?php
namespace Core\Http;

class Request
{
    public function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // Supprimer les query strings (?...)
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        $uri = rawurldecode($uri);

        return '/' . trim($uri, '/');
    }

    public function getMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    public function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public function post(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    public function query(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }
}
