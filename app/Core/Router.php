<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, array $handler, array $middleware): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $url = $this->getUrl();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->matchPath($route['path'], $url);
            if ($params === null) {
                continue;
            }

            foreach ($route['middleware'] as $mw) {
                $this->runMiddleware($mw);
            }

            [$controllerClass, $action] = $route['handler'];
            $controller = new $controllerClass();

            call_user_func_array([$controller, $action], $params);
            return;
        }

        http_response_code(404);
        View::render('errors/404', ['title' => 'Page Not Found']);
    }

    private function getUrl(): string
    {
        $url = $_GET['url'] ?? '';
        $url = trim((string) $url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return $url === false ? '' : $url;
    }

    private function matchPath(string $routePath, string $url): ?array
    {
        $routePath = trim($routePath, '/');
        $routeParts = $routePath === '' ? [] : explode('/', $routePath);
        $urlParts = $url === '' ? [] : explode('/', $url);

        if (count($routeParts) !== count($urlParts)) {
            return null;
        }

        $params = [];
        foreach ($routeParts as $i => $part) {
            if (preg_match('/^\{(.+)\}$/', $part, $matches)) {
                $params[$matches[1]] = $urlParts[$i];
                continue;
            }
            if ($part !== $urlParts[$i]) {
                return null;
            }
        }

        return array_values($params);
    }

    private function runMiddleware(string $middleware): void
    {
        if (str_contains($middleware, ':')) {
            [$class, $role] = explode(':', $middleware, 2);
            $instance = new $class($role);
        } else {
            $instance = new $middleware();
        }

        $instance->handle();
    }
}
