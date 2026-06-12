<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Session;

class RoleMiddleware
{
    private string $role;

    public function __construct(string $role)
    {
        $this->role = $role;
    }

    public function handle(): void
    {
        if ($this->role === 'admin' && !Auth::isAdmin()) {
            Session::flash('error', 'You do not have permission to access this page.');
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
            header('Location: ' . ($base === '' ? '' : $base) . '/');
            exit;
        }
    }
}
