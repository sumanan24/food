<?php
namespace App\Controllers;

use App\Models\SettingModel;
use Core\Controller;
use Core\Database;
use Core\Security;
use Core\Session;

class SettingController extends Controller
{
    public function index(): void
    {
        $this->requireRole('admin', 'super_admin');
        $this->view('settings.index', [
            'title'    => 'Settings',
            'settings' => (new SettingModel())->getAll(),
        ]);
    }

    public function update(): void
    {
        $this->requireRole('admin', 'super_admin');
        $this->validateCsrf();
        $keys = ['shop_name', 'shop_address', 'shop_phone', 'shop_email', 'currency', 'tax_rate', 'low_stock_threshold', 'invoice_prefix', 'theme'];
        $data = [];
        foreach ($keys as $key) {
            if (isset($_POST[$key])) {
                $data[$key] = Security::sanitize($_POST[$key]);
            }
        }
        (new SettingModel())->updateMany($data);
        $this->logActivity('update', 'settings');
        Session::flash('success', 'Settings saved.');
        redirect('settings');
    }

    public function backup(): void
    {
        $this->requireRole('super_admin');
        $this->validateCsrf();
        $config = require dirname(__DIR__, 2) . '/config/database.php';
        $backupDir = dirname(__DIR__, 2) . '/backups/';
        if (!is_dir($backupDir)) mkdir($backupDir, 0755, true);

        $file = $backupDir . 'backup_' . date('Y-m-d_His') . '.sql';
        $cmd = sprintf(
            '"%s" --user=%s --password=%s --host=%s %s > "%s"',
            'mysqldump',
            $config['username'],
            $config['password'],
            $config['host'],
            $config['dbname'],
            $file
        );
        // Fallback: PHP export of key tables
        $db = Database::getInstance();
        $tables = ['users', 'products', 'categories', 'sales', 'sale_items', 'purchases', 'suppliers', 'expenses', 'settings'];
        $sql = "-- Food Shop Backup " . date('Y-m-d H:i:s') . "\n";
        foreach ($tables as $table) {
            $rows = $db->query("SELECT * FROM $table")->fetchAll();
            foreach ($rows as $row) {
                $cols = implode(',', array_keys($row));
                $vals = implode(',', array_map(fn($v) => $db->quote($v), array_values($row)));
                $sql .= "INSERT INTO $table ($cols) VALUES ($vals);\n";
            }
        }
        file_put_contents($file, $sql);
        $this->logActivity('backup', 'settings', basename($file));
        Session::flash('success', 'Backup created: ' . basename($file));
        redirect('settings');
    }
}
