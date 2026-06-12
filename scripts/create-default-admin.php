<?php

declare(strict_types=1);

/**
 * Create default admin user and seed data.
 * Usage: php scripts/create-default-admin.php
 */

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');

require ROOT_PATH . '/vendor/autoload.php';

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = APP_PATH . '/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Database;
use App\Models\User;

const DEFAULT_NAME = 'Administrator';
const DEFAULT_EMAIL = 'admin@foodshop.com';
const DEFAULT_PASSWORD = 'admin123';

$configFile = CONFIG_PATH . '/database.local.php';

if (!file_exists($configFile)) {
    $config = [
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'foodshop',
        'username' => 'root',
        'password' => '',
    ];

    echo "No database config found. Using WAMP defaults (root / foodshop)...\n";

    try {
        Database::createDatabase($config);
        if (!is_dir(CONFIG_PATH)) {
            mkdir(CONFIG_PATH, 0755, true);
        }
        file_put_contents(
            $configFile,
            "<?php\n\ndeclare(strict_types=1);\n\nreturn " . var_export($config, true) . ";\n"
        );
        Database::reset();
        Database::runSqlFile(ROOT_PATH . '/sql/schema.sql');
        echo "Database and tables created.\n";
    } catch (Throwable $e) {
        fwrite(STDERR, "Database setup failed: {$e->getMessage()}\n");
        fwrite(STDERR, "Run the install wizard first or create config/database.local.php manually.\n");
        exit(1);
    }
}

$userModel = new User();

if ($userModel->findByEmail(DEFAULT_EMAIL)) {
    echo "Default admin already exists.\n";
    echo "Email:    " . DEFAULT_EMAIL . "\n";
    echo "Password: (use your existing password or reset via database)\n";
    exit(0);
}

$userModel->create([
    'name' => DEFAULT_NAME,
    'email' => DEFAULT_EMAIL,
    'password' => DEFAULT_PASSWORD,
    'role' => 'admin',
]);

$pdo = Database::getInstance();
$categories = ['Rent', 'Utilities', 'Salaries', 'Transport', 'Miscellaneous'];
$stmt = $pdo->prepare('INSERT IGNORE INTO expense_categories (name) VALUES (:name)');
foreach ($categories as $cat) {
    $stmt->execute(['name' => $cat]);
}

echo "Default admin created successfully!\n\n";
echo "Login URL:  http://localhost/food/public/login\n";
echo "Email:      " . DEFAULT_EMAIL . "\n";
echo "Password:   " . DEFAULT_PASSWORD . "\n\n";
echo "Change this password after first login.\n";
