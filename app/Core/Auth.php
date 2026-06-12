<?php

declare(strict_types=1);

namespace App\Core;

class Auth
{
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_role', $user['role']);
        Session::set('user_email', $user['email']);
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    public static function check(): bool
    {
        return Session::has('user_id');
    }

    public static function id(): ?int
    {
        $id = Session::get('user_id');
        return $id !== null ? (int) $id : null;
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        return [
            'id' => self::id(),
            'name' => Session::get('user_name'),
            'role' => Session::get('user_role'),
            'email' => Session::get('user_email'),
        ];
    }

    public static function isAdmin(): bool
    {
        return Session::get('user_role') === 'admin';
    }

    public static function isStaff(): bool
    {
        return Session::get('user_role') === 'staff';
    }
}
