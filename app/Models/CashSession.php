<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Services\StockService;

class CashSession extends Model
{
    public function isCounterOpen(): bool
    {
        return $this->getOpenSession() !== null;
    }

    /** Current open counter session (only one can be open at a time). */
    public function getOpenSession(): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT cs.*, u.name AS user_name FROM cash_sessions cs
             JOIN users u ON u.id = cs.user_id
             WHERE cs.status = \'open\'
             ORDER BY cs.opened_at DESC LIMIT 1'
        );
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** @deprecated Use getOpenSession() */
    public function getToday(): ?array
    {
        return $this->getOpenSession();
    }

    public function getTodaySessions(): array
    {
        $stmt = $this->db->prepare(
            'SELECT cs.*, u.name AS user_name FROM cash_sessions cs
             JOIN users u ON u.id = cs.user_id
             WHERE cs.session_date = CURDATE()
             ORDER BY cs.opened_at ASC'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByDate(string $date): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT cs.*, u.name AS user_name FROM cash_sessions cs
             JOIN users u ON u.id = cs.user_id
             WHERE cs.session_date = :date
             ORDER BY cs.opened_at DESC LIMIT 1'
        );
        $stmt->execute(['date' => $date]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function open(int $userId, float $openingBalance, string $counterPersonName, ?string $date = null): int
    {
        if ($this->getOpenSession() !== null) {
            throw new \RuntimeException('Bill counter is already open. Close it before opening again.');
        }

        $sessionDate = $date ?? date('Y-m-d');
        $counterPersonName = trim($counterPersonName);
        if ($counterPersonName === '') {
            throw new \RuntimeException('Counter person name is required.');
        }

        $stmt = $this->db->prepare(
            'INSERT INTO cash_sessions (session_date, user_id, counter_person_name, opening_balance, status)
             VALUES (:session_date, :user_id, :counter_person_name, :opening_balance, \'open\')'
        );
        $stmt->execute([
            'session_date' => $sessionDate,
            'user_id' => $userId,
            'counter_person_name' => $counterPersonName,
            'opening_balance' => $openingBalance,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function close(int $id, float $cashReceived, string $closedByName, ?string $notes = null): bool
    {
        $session = $this->find($id);
        if (!$session || $session['status'] === 'closed') {
            return false;
        }

        $closedByName = trim($closedByName);
        if ($closedByName === '') {
            return false;
        }

        $billModel = new Bill();
        $expenseModel = new Expense();
        $closedAt = date('Y-m-d H:i:s');
        $totalSales = $billModel->totalForSession($id);
        $totalExpenses = $expenseModel->totalDuringSession($session['opened_at'], $closedAt);
        $expectedCash = (float) $session['opening_balance'] + $totalSales - $totalExpenses;
        $closingBalance = $cashReceived;
        $cashDifference = $cashReceived - $expectedCash;

        $stmt = $this->db->prepare(
            'UPDATE cash_sessions SET total_sales = :total_sales, total_expenses = :total_expenses,
             cash_received = :cash_received, closing_balance = :closing_balance,
             cash_difference = :cash_difference, closed_by_name = :closed_by_name,
             status = \'closed\', closed_at = :closed_at, notes = :notes
             WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'total_sales' => $totalSales,
            'total_expenses' => $totalExpenses,
            'cash_received' => $cashReceived,
            'closing_balance' => $closingBalance,
            'cash_difference' => $cashDifference,
            'closed_by_name' => $closedByName,
            'closed_at' => $closedAt,
            'notes' => $notes,
        ]);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM cash_sessions WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function history(int $limit = 30): array
    {
        $stmt = $this->db->prepare(
            'SELECT cs.*, u.name AS user_name FROM cash_sessions cs
             JOIN users u ON u.id = cs.user_id
             ORDER BY cs.opened_at DESC LIMIT :lim'
        );
        $stmt->bindValue('lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
