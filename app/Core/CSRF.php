<?php

declare(strict_types=1);

namespace App\Core;

class CSRF
{
    private const TOKEN_KEY = '_csrf_token';

    public static function generate(): string
    {
        if (!Session::has(self::TOKEN_KEY)) {
            Session::set(self::TOKEN_KEY, bin2hex(random_bytes(32)));
        }

        return Session::get(self::TOKEN_KEY);
    }

    public static function validate(string $token): bool
    {
        $stored = Session::get(self::TOKEN_KEY, '');
        return $stored !== '' && hash_equals($stored, $token);
    }

    public static function field(): string
    {
        $token = self::generate();
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}
