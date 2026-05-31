<?php
namespace App\Models;

use Core\Model;

class SettingModel extends Model
{
    protected string $table = 'settings';

    public function getAll(): array
    {
        $rows = $this->db->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function get(string $key, string $default = ''): string
    {
        $stmt = $this->db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ? (string)$row['setting_value'] : $default;
    }

    public function set(string $key, string $value): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO settings (setting_key, setting_value) VALUES (?,?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
        );
        $stmt->execute([$key, $value]);
    }

    public function updateMany(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, (string)$value);
        }
    }
}
