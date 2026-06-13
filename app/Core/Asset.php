<?php

declare(strict_types=1);

namespace App\Core;

class Asset
{
    public static function url(string $path): string
    {
        $path = ltrim($path, '/');
        $baseUrl = View::url('assets/' . $path);
        $filePath = self::resolvePath($path);

        if ($filePath !== null && is_file($filePath)) {
            return $baseUrl . '?v=' . filemtime($filePath);
        }

        $config = file_exists(CONFIG_PATH . '/app.php')
            ? require CONFIG_PATH . '/app.php'
            : [];

        return $baseUrl . '?v=' . urlencode((string) ($config['asset_version'] ?? '1'));
    }

    private static function resolvePath(string $path): ?string
    {
        $publicPath = defined('PUBLIC_PATH')
            ? PUBLIC_PATH
            : (defined('ROOT_PATH') ? ROOT_PATH . '/public' : null);

        if ($publicPath === null) {
            return null;
        }

        $full = $publicPath . '/assets/' . str_replace(['../', '..\\'], '', $path);

        return is_file($full) ? $full : null;
    }
}
