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
        // Already a clean path from index.php (?url=login)
        if (!isset($_GET['url']) || $_GET['url'] === '') {
            $uri = parse_url($uri, PHP_URL_PATH) ?? '/';

            // Auto-detect install folder from index.php location
            $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
            $basePath = rtrim(dirname($scriptName), '/');
            if ($basePath === '/' || $basePath === '.') {
                $basePath = '';
            }
            if ($basePath !== '' && strpos($uri, $basePath) === 0) {
                $uri = substr($uri, strlen($basePath)) ?: '/';
            }

            // Root URL rewrite (/food/ -> public/) leaves REQUEST_URI as /food/...
            $projectBase = dirname($basePath);
            if ($projectBase !== '' && $projectBase !== '/' && $projectBase !== '.'
                && strpos($uri, $projectBase) === 0) {
                $uri = substr($uri, strlen($projectBase)) ?: '/';
            }

            // Direct access: /food/public/index.php
            if (strpos($uri, '/index.php') === 0) {
                $uri = substr($uri, 10) ?: '/';
            }
        }

        $uri = '/' . trim($uri, '/');
        $uri = rtrim($uri, '/') ?: '/';

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
