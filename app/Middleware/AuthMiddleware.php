<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Session;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            Session::flash('error', 'Please login to continue.');
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
            header('Location: ' . ($base === '' ? '' : $base) . '/login');
            exit;
        }
    }
}
