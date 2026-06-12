<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Item extends Model
{
    public function all(): array
    {
        return $this->db->query(
            'SELECT * FROM items WHERE is_active = 1 ORDER BY name ASC'
        )->fetchAll();
    }

    public function allWithInactive(): array
    {
        return $this->db->query('SELECT * FROM items ORDER BY name ASC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM items WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $item = $stmt->fetch();
        return $item ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO items (name, cost_price, selling_price, current_stock) 
             VALUES (:name, :cost_price, :selling_price, :current_stock)'
        );
        $stmt->execute([
            'name' => $data['name'],
            'cost_price' => $data['cost_price'],
            'selling_price' => $data['selling_price'],
            'current_stock' => $data['current_stock'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE items SET name = :name, cost_price = :cost_price, 
             selling_price = :selling_price, current_stock = :current_stock WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'cost_price' => $data['cost_price'],
            'selling_price' => $data['selling_price'],
            'current_stock' => $data['current_stock'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE items SET is_active = 0 WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function increaseStock(int $id, float $quantity): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE items SET current_stock = current_stock + :qty WHERE id = :id'
        );
        return $stmt->execute(['id' => $id, 'qty' => $quantity]);
    }

    public function decreaseStock(int $id, float $quantity): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE items SET current_stock = current_stock - :qty 
             WHERE id = :id AND current_stock >= :qty'
        );
        return $stmt->execute(['id' => $id, 'qty' => $quantity]);
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM items WHERE name = :name';
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
