<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\User;

class InstallController extends Controller
{
    public function __construct()
    {
        require_once APP_PATH . '/Helpers/functions.php';
    }

    public function index(): void
    {
        if (file_exists(CONFIG_PATH . '/database.local.php')) {
            $userModel = new User();
            if ($userModel->countAll() > 0) {
                $this->redirect('/login');
            }
            Session::set('install_step', 2);
        }

        $this->view('install/index', [
            'title' => 'Installation Wizard',
            'step' => (int) Session::get('install_step', 1),
            'dbConfig' => Session::get('install_db', []),
        ], 'layouts/install');
    }

    public function database(): void
    {
        if (file_exists(CONFIG_PATH . '/database.local.php')) {
            $this->redirect('/login');
        }

        $config = [
            'host' => trim((string) $this->input('host', 'localhost')),
            'port' => (int) $this->input('port', 3306),
            'database' => trim((string) $this->input('database', '')),
            'username' => trim((string) $this->input('username', '')),
            'password' => (string) $this->input('password', ''),
        ];

        if ($config['database'] === '' || $config['username'] === '') {
            Session::flash('error', 'Database name and username are required.');
            $this->redirect('/install');
        }

        if (!Database::testConnection($config)) {
            Session::flash('error', 'Could not connect to database server. Check your credentials.');
            $this->redirect('/install');
        }

        try {
            Database::createDatabase($config);

            $configContent = "<?php\n\ndeclare(strict_types=1);\n\nreturn " . var_export($config, true) . ";\n";
            if (!is_dir(CONFIG_PATH)) {
                mkdir(CONFIG_PATH, 0755, true);
            }
            file_put_contents(CONFIG_PATH . '/database.local.php', $configContent);

            Database::reset();
            Database::runSqlFile(ROOT_PATH . '/sql/schema.sql');
            Database::runSqlFile(ROOT_PATH . '/sql/seed.sql');

            Session::set('install_db', $config);
            Session::set('install_step', 2);
            Session::flash('success', 'Database configured. Default admin: admin@foodshop.com / admin123');
        } catch (\Throwable $e) {
            @unlink(CONFIG_PATH . '/database.local.php');
            Session::flash('error', 'Installation failed: ' . $e->getMessage());
        }

        $this->redirect('/install');
    }

    public function admin(): void
    {
        if (!file_exists(CONFIG_PATH . '/database.local.php')) {
            Session::flash('error', 'Please complete database setup first.');
            $this->redirect('/install');
        }

        $userModel = new User();
        if ($userModel->countAll() > 0) {
            Session::flash('success', 'System already installed. Please login.');
            $this->redirect('/login');
        }

        $name = trim((string) $this->input('name', ''));
        $email = trim((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');
        $confirm = (string) $this->input('confirm_password', '');

        if ($name === '' || $email === '' || $password === '') {
            Session::flash('error', 'All fields are required.');
            $this->redirect('/install');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please enter a valid email address.');
            $this->redirect('/install');
        }

        if (strlen($password) < 6) {
            Session::flash('error', 'Password must be at least 6 characters.');
            $this->redirect('/install');
        }

        if ($password !== $confirm) {
            Session::flash('error', 'Password confirmation does not match.');
            $this->redirect('/install');
        }

        $userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
        ]);

        // Seed default expense categories
        $pdo = Database::getInstance();
        $categories = ['Rent', 'Utilities', 'Salaries', 'Transport', 'Miscellaneous'];
        $stmt = $pdo->prepare('INSERT IGNORE INTO expense_categories (name) VALUES (:name)');
        foreach ($categories as $cat) {
            $stmt->execute(['name' => $cat]);
        }

        Session::remove('install_step');
        Session::remove('install_db');
        Session::flash('success', 'Installation complete! You can now login.');
        $this->redirect('/login');
    }
}
