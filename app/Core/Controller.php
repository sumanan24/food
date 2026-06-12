<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'layouts/main'): void
    {
        View::render($view, $data, $layout);
    }

    protected function redirect(string $path): void
    {
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
        $url = ($base === '' ? '' : $base) . '/' . ltrim($path, '/');
        header('Location: ' . $url);
        exit;
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function validateCsrf(): void
    {
        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::validate($token)) {
            Session::flash('error', 'Invalid security token. Please try again.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
}
