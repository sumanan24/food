<?php
namespace App\Models;

use Core\Model;

class SupplierModel extends Model
{
    protected string $table = 'suppliers';

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO suppliers (name, contact_number, address, email, status) VALUES (?,?,?,?,?)"
        );
        $stmt->execute([
            $data['name'], $data['contact_number'] ?? null,
            $data['address'] ?? null, $data['email'] ?? null, $data['status'] ?? 1,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE suppliers SET name=?, contact_number=?, address=?, email=?, status=? WHERE id=?"
        );
        return $stmt->execute([
            $data['name'], $data['contact_number'] ?? null,
            $data['address'] ?? null, $data['email'] ?? null, $data['status'] ?? 1, $id,
        ]);
    }

    public function active(): array
    {
        return $this->db->query("SELECT * FROM suppliers WHERE status = 1 ORDER BY name")->fetchAll();
    }
}
