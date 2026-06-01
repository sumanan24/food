<?php
namespace Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            if (!function_exists('db_config')) {
                require_once dirname(__DIR__) . '/core/bootstrap.php';
            }
            $config = db_config();
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
            try {
                self::$instance = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                error_log('[Food Shop] DB: ' . $e->getMessage());
                if (function_exists('app_debug') && app_debug()) {
                    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
                }
                die('Database connection failed. Configure config/database.local.php on the server.');
            }
        }
        return self::$instance;
    }
}
