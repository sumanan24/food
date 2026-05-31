<?php
namespace App\Models;

use Core\Model;

class CategoryModel extends Model
{
    protected string $table = 'categories';

    public function search(string $q): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM categories WHERE name LIKE ? OR description LIKE ? ORDER BY name"
        );
        $like = "%$q%";
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO categories (name, description, status) VALUES (?,?,?)");
        $stmt->execute([$data['name'], $data['description'] ?? null, $data['status'] ?? 1]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE categories SET name=?, description=?, status=? WHERE id=?");
        return $stmt->execute([$data['name'], $data['description'] ?? null, $data['status'] ?? 1, $id]);
    }

    public function active(): array
    {
        return $this->db->query("SELECT * FROM categories WHERE status = 1 ORDER BY name")->fetchAll();
    }
}
