<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Expense extends Model
{
    public function all(): array
    {
        return $this->db->query(
            'SELECT e.*, c.name AS category_name, u.name AS user_name
             FROM expenses e
             JOIN expense_categories c ON c.id = e.category_id
             JOIN users u ON u.id = e.user_id
             ORDER BY e.expense_date DESC, e.id DESC'
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM expenses WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO expenses (category_id, user_id, title, amount, expense_date, notes)
             VALUES (:category_id, :user_id, :title, :amount, :expense_date, :notes)'
        );
        $stmt->execute([
            'category_id' => $data['category_id'],
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'amount' => $data['amount'],
            'expense_date' => $data['expense_date'],
            'notes' => $data['notes'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE expenses SET category_id = :category_id, title = :title, 
             amount = :amount, expense_date = :expense_date, notes = :notes WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'amount' => $data['amount'],
            'expense_date' => $data['expense_date'],
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM expenses WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function todayTotal(): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE expense_date = CURDATE()'
        );
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    }

    public function totalDuringSession(string $openedAt, ?string $closedAt = null): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(amount), 0) FROM expenses
             WHERE created_at >= :opened_at AND created_at <= :closed_at'
        );
        $stmt->execute([
            'opened_at' => $openedAt,
            'closed_at' => $closedAt ?? date('Y-m-d H:i:s'),
        ]);
        return (float) $stmt->fetchColumn();
    }

    public function totalBetween(string $start, string $end): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(amount), 0) FROM expenses 
             WHERE expense_date BETWEEN :start AND :end'
        );
        $stmt->execute(['start' => $start, 'end' => $end]);
        return (float) $stmt->fetchColumn();
    }

    public function between(string $start, string $end): array
    {
        $stmt = $this->db->prepare(
            'SELECT e.*, c.name AS category_name, u.name AS user_name
             FROM expenses e
             JOIN expense_categories c ON c.id = e.category_id
             JOIN users u ON u.id = e.user_id
             WHERE e.expense_date BETWEEN :start AND :end
             ORDER BY e.expense_date DESC'
        );
        $stmt->execute(['start' => $start, 'end' => $end]);
        return $stmt->fetchAll();
    }

    public function chartData(string $start, string $end): array
    {
        $stmt = $this->db->prepare(
            'SELECT expense_date AS label, SUM(amount) AS total
             FROM expenses
             WHERE expense_date BETWEEN :start AND :end
             GROUP BY expense_date
             ORDER BY expense_date ASC'
        );
        $stmt->execute(['start' => $start, 'end' => $end]);
        return $stmt->fetchAll();
    }
}
