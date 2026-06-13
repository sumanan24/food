<?php

declare(strict_types=1);

namespace App\Core;

class PdfLoader
{
    private static bool $loaded = false;

    public static function load(): void
    {
        if (self::$loaded) {
            return;
        }

        $file = LIB_PATH . '/tcpdf/tcpdf.php';
        if (!file_exists($file)) {
            throw new \RuntimeException('PDF library not found at lib/tcpdf/.');
        }

        require_once $file;
        self::$loaded = true;
    }
}
