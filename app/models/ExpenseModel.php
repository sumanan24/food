<?php
namespace App\Models;

use Core\Model;

class ExpenseModel extends Model
{
    protected string $table = 'expenses';

    public function getAll(array $filters = []): array
    {
        $sql = "SELECT * FROM expenses WHERE 1=1";
        $params = [];
        if (!empty($filters['from'])) {
            $sql .= " AND expense_date >= ?";
            $params[] = $filters['from'];
        }
        if (!empty($filters['to'])) {
            $sql .= " AND expense_date <= ?";
            $params[] = $filters['to'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (title LIKE ? OR category_name LIKE ?)";
            $like = '%' . $filters['search'] . '%';
            $params[] = $like;
            $params[] = $like;
        }
        $sql .= " ORDER BY expense_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO expenses (title, category_id, category_name, amount, expense_date, notes, user_id) VALUES (?,?,?,?,?,?,?)"
        );
        $stmt->execute([
            $data['title'], $data['category_id'] ?: null, $data['category_name'] ?? null,
            $data['amount'], $data['expense_date'], $data['notes'] ?? null, $data['user_id'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE expenses SET title=?, category_id=?, category_name=?, amount=?, expense_date=?, notes=? WHERE id=?"
        );
        return $stmt->execute([
            $data['title'], $data['category_id'] ?: null, $data['category_name'] ?? null,
            $data['amount'], $data['expense_date'], $data['notes'] ?? null, $id,
        ]);
    }

    public function totalExpenses(string $from, string $to): float
    {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount),0) FROM expenses WHERE expense_date BETWEEN ? AND ?");
        $stmt->execute([$from, $to]);
        return (float) $stmt->fetchColumn();
    }

    public function categories(): array
    {
        return $this->db->query("SELECT * FROM expense_categories WHERE status = 1 ORDER BY name")->fetchAll();
    }
}
