<?php
namespace Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, string $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    private function add(string $method, string $path, string $handler, array $middleware): void
    {
        $this->routes[] = compact('method', 'path', 'handler', 'middleware');
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = $this->normalizeUri($uri);

        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\{([a-z]+)\}/', '([^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $this->runMiddleware($route['middleware']);
                $this->callHandler($route['handler'], $matches);
                return;
            }
        }

        http_response_code(404);
        require dirname(__DIR__) . '/app/views/errors/404.php';
    }

    private function normalizeUri(string $uri): string
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?? $uri;
        if ($uri === '' || $uri[0] !== '/') {
            $uri = '/' . ltrim($uri, '/');
        }

        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $basePath = rtrim(dirname($scriptName), '/');
        if ($basePath === '/' || $basePath === '.') {
            $basePath = '';
        }
        if ($basePath !== '' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath)) ?: '/';
        }

        $projectBase = dirname($basePath);
        if ($projectBase !== '' && $projectBase !== '/' && $projectBase !== '.'
            && strpos($uri, $projectBase) === 0) {
            $uri = substr($uri, strlen($projectBase)) ?: '/';
        }

        // /food/public/food/ → ?url=food — treat install folder name as app root
        $projectFolder = $basePath !== '' ? basename(dirname($basePath)) : '';
        if ($projectFolder !== '' && $projectFolder !== 'public' && $projectFolder !== 'www') {
            $folderPath = '/' . $projectFolder;
            if ($uri === $folderPath || $uri === $folderPath . '/') {
                $uri = '/';
            } elseif (strpos($uri, $folderPath . '/') === 0) {
                $uri = substr($uri, strlen($folderPath)) ?: '/';
            }
        }

        if (strpos($uri, '/index.php') === 0) {
            $uri = substr($uri, 10) ?: '/';
        }

        $uri = '/' . trim($uri, '/');
        return rtrim($uri, '/') ?: '/';
    }

    private function runMiddleware(array $middleware): void
    {
        foreach ($middleware as $mw) {
            if ($mw === 'auth' && !isLoggedIn()) {
                redirect('login');
            }
            if ($mw === 'admin' && !hasRole('admin', 'super_admin')) {
                Session::flash('error', 'Admin access required.');
                redirect(isCashier() ? 'sales' : 'dashboard');
            }
            // Cashiers: POS / sales billing only (no edit/delete elsewhere)
            if ($mw === 'staff' && isCashier()) {
                Session::flash('error', 'Cashiers can only use POS billing. Edit and delete are not allowed.');
                redirect('sales');
            }
        }
    }

    private function callHandler(string $handler, array $params): void
    {
        [$controller, $method] = explode('@', $handler);
        $class = "App\\Controllers\\{$controller}";
        if (!class_exists($class)) {
            die("Controller not found: $class");
        }
        $instance = new $class();
        call_user_func_array([$instance, $method], $params);
    }
}
