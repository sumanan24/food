<?php

declare(strict_types=1);

use App\Core\CSRF;
use App\Core\View;

function e(?string $value): string
{
    return View::escape($value);
}

function money(float|int|string $amount): string
{
    return View::money($amount);
}

function url(string $path = ''): string
{
    return View::url($path);
}

function csrf_field(): string
{
    return CSRF::field();
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

function set_old(array $data): void
{
    $_SESSION['_old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}
