<?php
namespace Core\Http;

class Router
{
    private array $routes = [];

    public function add(string $method, string $path, callable $callback): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => '/' . trim($path, '/'),
            'callback' => $callback
        ];
    }

    public function dispatch(Request $request)
    {
        $uri = $request->getUri();
        $method = $request->getMethod();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if ($route['path'] === $uri) {
                return call_user_func($route['callback'], $request);
            }

            // Support basique des wildcards pour les sous-pages (ex: /admin/*)
            if (strpos($route['path'], '*') !== false) {
                $pattern = str_replace('\*', '.*', preg_quote($route['path'], '/'));
                if (preg_match('/^' . $pattern . '$/', $uri)) {
                    return call_user_func($route['callback'], $request);
                }
            }
        }

        // 404
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Page Not Found</h1>";
    }

    public function redirect(string $url): void
    {
        if (!str_starts_with($url, '/')) {
            throw new \InvalidArgumentException('Redirect URL must be an internal path.');
        }

        header("Location: $url");
        exit;
    }
}
