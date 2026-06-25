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

    /** Apply incremental schema updates on existing installations. */
    public static function ensureMigrations(): void
    {
        if (!self::isSchemaReady()) {
            return;
        }

        $pdo = self::getInstance();
        if (!$pdo->query("SHOW TABLES LIKE 'cash_sessions'")->fetch()) {
            return;
        }

        self::migrateCashCounterV3($pdo);
        self::migrateCashMultiSessionsV4($pdo);
    }

    private static function migrateCashCounterV3(PDO $pdo): void
    {
        if (!self::columnExists($pdo, 'cash_sessions', 'counter_person_name')) {
            $pdo->exec(
                "ALTER TABLE `cash_sessions`
                 ADD COLUMN `counter_person_name` VARCHAR(100) NOT NULL DEFAULT '' AFTER `user_id`"
            );
        }

        if (!self::columnExists($pdo, 'cash_sessions', 'closed_by_name')) {
            $pdo->exec(
                "ALTER TABLE `cash_sessions`
                 ADD COLUMN `closed_by_name` VARCHAR(100) NULL AFTER `cash_difference`"
            );
        }
    }

    private static function migrateCashMultiSessionsV4(PDO $pdo): void
    {
        if (self::indexExists($pdo, 'cash_sessions', 'uk_cash_session_date')) {
            $pdo->exec('ALTER TABLE `cash_sessions` DROP INDEX `uk_cash_session_date`');
        }

        if (!self::indexExists($pdo, 'cash_sessions', 'idx_cash_sessions_date_status')) {
            $pdo->exec(
                'ALTER TABLE `cash_sessions`
                 ADD KEY `idx_cash_sessions_date_status` (`session_date`, `status`)'
            );
        }

        if (!self::columnExists($pdo, 'bills', 'cash_session_id')) {
            $pdo->exec(
                'ALTER TABLE `bills`
                 ADD COLUMN `cash_session_id` INT UNSIGNED NULL AFTER `user_id`'
            );
        }

        if (!self::indexExists($pdo, 'bills', 'idx_bills_cash_session')) {
            $pdo->exec(
                'ALTER TABLE `bills`
                 ADD KEY `idx_bills_cash_session` (`cash_session_id`)'
            );
        }

        if (!self::foreignKeyExists($pdo, 'bills', 'fk_bills_cash_session')) {
            $pdo->exec(
                'ALTER TABLE `bills`
                 ADD CONSTRAINT `fk_bills_cash_session`
                 FOREIGN KEY (`cash_session_id`) REFERENCES `cash_sessions` (`id`) ON DELETE SET NULL'
            );
        }
    }

    private static function columnExists(PDO $pdo, string $table, string $column): bool
    {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :column'
        );
        $stmt->execute(['table' => $table, 'column' => $column]);

        return (int) $stmt->fetchColumn() > 0;
    }

    private static function indexExists(PDO $pdo, string $table, string $indexName): bool
    {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.statistics
             WHERE table_schema = DATABASE() AND table_name = :table AND index_name = :index'
        );
        $stmt->execute(['table' => $table, 'index' => $indexName]);

        return (int) $stmt->fetchColumn() > 0;
    }

    private static function foreignKeyExists(PDO $pdo, string $table, string $constraintName): bool
    {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM information_schema.table_constraints
             WHERE table_schema = DATABASE() AND table_name = :table
             AND constraint_name = :name AND constraint_type = 'FOREIGN KEY'"
        );
        $stmt->execute(['table' => $table, 'name' => $constraintName]);

        return (int) $stmt->fetchColumn() > 0;
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
