<?php
namespace App\Models;

use Core\Model;
use PDO;

class UserModel extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? AND status = 1 LIMIT 1");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, phone, role, status) VALUES (?,?,?,?,?,?)"
        );
        $stmt->execute([
            $data['name'], $data['email'], $data['password'],
            $data['phone'] ?? null, $data['role'], $data['status'] ?? 1,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = ['name = ?', 'email = ?', 'phone = ?', 'role = ?', 'status = ?'];
        $params = [$data['name'], $data['email'], $data['phone'] ?? null, $data['role'], $data['status']];
        if (!empty($data['password'])) {
            $fields[] = 'password = ?';
            $params[] = $data['password'];
        }
        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        return $this->db->prepare($sql)->execute($params);
    }

    public function search(string $q): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, phone, role, status, last_login, created_at FROM users
             WHERE name LIKE ? OR email LIKE ? ORDER BY id DESC"
        );
        $like = "%$q%";
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }

    public function setRememberToken(int $id, string $token): void
    {
        $this->db->prepare("UPDATE users SET remember_token = ? WHERE id = ?")->execute([$token, $id]);
    }

    public function findByRememberToken(string $token): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE remember_token = ? AND status = 1 LIMIT 1");
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }
}
