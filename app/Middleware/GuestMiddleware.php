<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;

class GuestMiddleware
{
    public function handle(): void
    {
        if (Auth::check()) {
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
            header('Location: ' . ($base === '' ? '' : $base) . '/');
            exit;
        }
    }
}
