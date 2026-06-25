<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $configFile = CONFIG_PATH . '/database.local.php';
            if (!file_exists($configFile)) {
                throw new PDOException('Database not configured. Run the installation wizard.');
            }

            $config = require $configFile;

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $config['host'],
                $config['port'] ?? 3306,
                $config['database']
            );

            self::$instance = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }

        return self::$instance;
    }

    public static function isSchemaReady(): bool
    {
        try {
            if (!file_exists(CONFIG_PATH . '/database.local.php')) {
                return false;
            }
            $pdo = self::getInstance();
            $result = $pdo->query("SHOW TABLES LIKE 'users'");
            return $result !== false && $result->rowCount() > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    public static function isInstalled(): bool
    {
        if (!self::isSchemaReady()) {
            return false;
        }
        try {
            $count = (int) self::getInstance()->query('SELECT COUNT(*) FROM users')->fetchColumn();
            return $count > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    public static function ensureSchema(): void
    {
        if (self::isSchemaReady()) {
            return;
        }

        self::runSqlFile(ROOT_PATH . '/sql/schema.sql');
        if (file_exists(ROOT_PATH . '/sql/seed.sql')) {
            self::runSqlFile(ROOT_PATH . '/sql/seed.sql');
        }
    }

    public static function reset(): void
    {
        self::$instance = null;
    }

    public static function testConnection(array $config): bool
    {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;charset=utf8mb4',
                $config['host'],
                $config['port'] ?? 3306
            );

            $pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            return true;
        } catch (PDOException) {
            return false;
        }
    }

    public static function createDatabase(array $config): void
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;charset=utf8mb4',
            $config['host'],
            $config['port'] ?? 3306
        );

        $pdo = new PDO(
            $dsn,
            $config['username'],
            $config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $dbName = $config['database'];
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    public static function runSqlFile(string $filePath, ?array $config = null): void
    {
        if ($config !== null) {
            self::reset();
            $configFile = CONFIG_PATH . '/database.local.php';
            if (!file_exists($configFile)) {
                file_put_contents($configFile, '<?php return ' . var_export($config, true) . ';');
            }
            self::reset();
        }

        $pdo = $config !== null
            ? self::createTempPdo($config)
            : self::getInstance();

        $sql = file_get_contents($filePath);
        if ($sql === false) {
            throw new PDOException('Could not read SQL file.');
        }

        // Strip single-line SQL comments so statements are not skipped.
        $sql = preg_replace('/^\s*--.*$/m', '', $sql) ?? $sql;

        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            fn(string $s) => $s !== ''
        );

        foreach ($statements as $statement) {
            if ($statement !== '') {
                $pdo->exec($statement);
            }
        }
    }

    private static function createTempPdo(array $config): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['port'] ?? 3306,
            $config['database']
        );

        return new PDO(
            $dsn,
            $config['username'],
            $config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
}
