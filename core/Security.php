<?php
namespace Core;

class Security
{
    public static function csrfToken(): string
    {
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    public static function csrfField(): string
    {
        $name = (require dirname(__DIR__) . '/config/app.php')['csrf_token_name'];
        $token = self::csrfToken();
        return '<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($token) . '">';
    }

    public static function validateCsrf(?string $token): bool
    {
        $stored = Session::get('csrf_token');
        return $stored && $token && hash_equals($stored, $token);
    }

    public static function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }

    public static function sanitize(string $input): string
    {
        return trim(strip_tags($input));
    }

    public static function validateRequired(array $fields, array $data): array
    {
        $errors = [];
        foreach ($fields as $field => $label) {
            if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
                $errors[$field] = "$label is required.";
            }
        }
        return $errors;
    }
}
