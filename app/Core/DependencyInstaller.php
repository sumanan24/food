<?php

declare(strict_types=1);

namespace App\Core;

class DependencyInstaller
{
    private string $rootPath;

    public function __construct(string $rootPath)
    {
        $this->rootPath = rtrim($rootPath, '/\\');
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function install(): array
    {
        $autoload = $this->rootPath . '/vendor/autoload.php';
        if (file_exists($autoload)) {
            return ['success' => true, 'message' => 'Dependencies already installed.'];
        }

        $lockFile = $this->rootPath . '/storage/.dependency-install.lock';
        if (file_exists($lockFile) && (time() - (int) filemtime($lockFile)) < 300) {
            return [
                'success' => false,
                'message' => 'Dependency installation is already in progress. Refresh in a minute.',
            ];
        }

        $this->ensureStorageDir();
        touch($lockFile);

        try {
            if ($this->runComposerInstall()) {
                return ['success' => true, 'message' => 'Dependencies installed via Composer.'];
            }

            if ($this->installTcpdfBundle()) {
                return ['success' => true, 'message' => 'Dependencies installed (TCPDF bundle).'];
            }

            return [
                'success' => false,
                'message' => 'Auto-install failed. Enable PHP zip/curl extensions or run composer install via SSH.',
            ];
        } finally {
            @unlink($lockFile);
        }
    }

    private function ensureStorageDir(): void
    {
        $dir = $this->rootPath . '/storage';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    private function runComposerInstall(): bool
    {
        if (!$this->canRunShell()) {
            return false;
        }

        $composerPhar = $this->rootPath . '/composer.phar';
        if (!file_exists($composerPhar)) {
            if (!$this->downloadComposerPhar($composerPhar)) {
                return false;
            }
        }

        $php = $this->phpBinary();
        $command = sprintf(
            '%s %s install --no-dev --no-ansi --no-interaction --working-dir=%s 2>&1',
            escapeshellarg($php),
            escapeshellarg($composerPhar),
            escapeshellarg($this->rootPath)
        );

        $output = $this->runCommand($command);
        return file_exists($this->rootPath . '/vendor/autoload.php');
    }

    private function installTcpdfBundle(): bool
    {
        if (!class_exists(ZipArchive::class)) {
            return false;
        }

        $vendorDir = $this->rootPath . '/vendor';
        $tcpdfDir = $vendorDir . '/tecnickcom/tcpdf';

        if (!is_dir($vendorDir)) {
            mkdir($vendorDir, 0755, true);
        }
        if (!is_dir($vendorDir . '/tecnickcom')) {
            mkdir($vendorDir . '/tecnickcom', 0755, true);
        }

        if (!is_dir($tcpdfDir) || !file_exists($tcpdfDir . '/tcpdf.php')) {
            $zipUrl = 'https://github.com/tecnickcom/TCPDF/archive/refs/tags/6.11.3.zip';
            $zipFile = sys_get_temp_dir() . '/tcpdf-' . uniqid('', true) . '.zip';

            if (!$this->downloadFile($zipUrl, $zipFile)) {
                return false;
            }

            $zip = new \ZipArchive();
            if ($zip->open($zipFile) !== true) {
                @unlink($zipFile);
                return false;
            }

            $extractTo = sys_get_temp_dir() . '/tcpdf-extract-' . uniqid('', true);
            mkdir($extractTo, 0755, true);
            $zip->extractTo($extractTo);
            $zip->close();
            @unlink($zipFile);

            $extracted = $this->findExtractedTcpdfDir($extractTo);
            if ($extracted === null) {
                $this->removeDirectory($extractTo);
                return false;
            }

            if (is_dir($tcpdfDir)) {
                $this->removeDirectory($tcpdfDir);
            }

            rename($extracted, $tcpdfDir);
            $this->removeDirectory($extractTo);
        }

        if (!file_exists($tcpdfDir . '/tcpdf.php')) {
            return false;
        }

        $this->writeMinimalAutoload($vendorDir);
        return file_exists($vendorDir . '/autoload.php');
    }

    private function findExtractedTcpdfDir(string $extractTo): ?string
    {
        $entries = scandir($extractTo);
        if ($entries === false) {
            return null;
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $path = $extractTo . '/' . $entry;
            if (is_dir($path) && file_exists($path . '/tcpdf.php')) {
                return $path;
            }
        }

        return null;
    }

    private function writeMinimalAutoload(string $vendorDir): void
    {
        $content = <<<'PHP'
<?php

declare(strict_types=1);

// Auto-generated minimal autoloader (no Composer required on server)

$tcpdfBootstrap = __DIR__ . '/tecnickcom/tcpdf/tcpdf.php';
if (is_file($tcpdfBootstrap)) {
    require_once $tcpdfBootstrap;
}

spl_autoload_register(static function (string $class): void {
    if ($class === 'TCPDF' && is_file(__DIR__ . '/tecnickcom/tcpdf/tcpdf.php')) {
        require_once __DIR__ . '/tecnickcom/tcpdf/tcpdf.php';
    }
});

PHP;

        file_put_contents($vendorDir . '/autoload.php', $content);
    }

    private function downloadComposerPhar(string $destination): bool
    {
        return $this->downloadFile('https://getcomposer.org/download/latest-stable/composer.phar', $destination);
    }

    private function downloadFile(string $url, string $destination): bool
    {
        if (function_exists('curl_init')) {
            $fp = fopen($destination, 'wb');
            if ($fp === false) {
                return false;
            }

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_FILE => $fp,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 300,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_USERAGENT => 'FoodShop-Installer/1.0',
            ]);
            $ok = curl_exec($ch) !== false && curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200;
            curl_close($ch);
            fclose($fp);

            if (!$ok) {
                @unlink($destination);
            }
            return $ok;
        }

        if (!ini_get('allow_url_fopen')) {
            return false;
        }

        $context = stream_context_create([
            'http' => ['timeout' => 300, 'user_agent' => 'FoodShop-Installer/1.0'],
            'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
        ]);

        $data = @file_get_contents($url, false, $context);
        if ($data === false) {
            return false;
        }

        return file_put_contents($destination, $data) !== false;
    }

    private function canRunShell(): bool
    {
        if (!function_exists('proc_open') && !function_exists('shell_exec') && !function_exists('exec')) {
            return false;
        }

        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));
        $needed = ['proc_open', 'shell_exec', 'exec', 'passthru', 'system'];
        foreach ($needed as $fn) {
            if (function_exists($fn) && !in_array($fn, $disabled, true)) {
                return true;
            }
        }

        return false;
    }

    private function phpBinary(): string
    {
        if (defined('PHP_BINARY') && PHP_BINARY !== '') {
            return PHP_BINARY;
        }

        return PHP_OS_FAMILY === 'Windows' ? 'php' : '/usr/bin/php';
    }

    private function runCommand(string $command): string
    {
        if (function_exists('proc_open')) {
            $descriptors = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
            $process = proc_open($command, $descriptors, $pipes, $this->rootPath);
            if (is_resource($process)) {
                $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
                foreach ($pipes as $pipe) {
                    fclose($pipe);
                }
                proc_close($process);
                return $output ?: '';
            }
        }

        if (function_exists('shell_exec')) {
            return (string) shell_exec($command);
        }

        if (function_exists('exec')) {
            exec($command, $output);
            return implode("\n", $output);
        }

        return '';
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($dir);
    }
}
