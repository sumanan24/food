<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class ExpenseCategory extends Model
{
    public function all(): array
    {
        return $this->db->query('SELECT * FROM expense_categories ORDER BY name ASC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM expense_categories WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO expense_categories (name, description) VALUES (:name, :description)'
        );
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE expense_categories SET name = :name, description = :description WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM expenses WHERE category_id = :id');
        $stmt->execute(['id' => $id]);
        if ((int) $stmt->fetchColumn() > 0) {
            return false;
        }
        $del = $this->db->prepare('DELETE FROM expense_categories WHERE id = :id');
        return $del->execute(['id' => $id]);
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM expense_categories WHERE name = :name';
        $params = ['name' => $name];
        if ($excludeId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
}
