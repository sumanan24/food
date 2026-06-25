<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Services\StockService;

class Purchase extends Model
{
    public function create(array $data): int
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO purchases (item_id, user_id, supplier_name, quantity, unit_cost, total_cost, purchase_date, notes)
                 VALUES (:item_id, :user_id, :supplier_name, :quantity, :unit_cost, :total_cost, :purchase_date, :notes)'
            );
            $stmt->execute([
                'item_id' => $data['item_id'],
                'user_id' => $data['user_id'],
                'supplier_name' => $data['supplier_name'] ?? null,
                'quantity' => $data['quantity'],
                'unit_cost' => $data['unit_cost'],
                'total_cost' => $data['total_cost'],
                'purchase_date' => $data['purchase_date'],
                'notes' => $data['notes'] ?? null,
            ]);
            $id = (int) $this->db->lastInsertId();

            $stockService = new StockService();
            $stockService->recordPurchase(
                (int) $data['item_id'],
                (int) $data['user_id'],
                (float) $data['quantity'],
                $id,
                $data['purchase_date']
            );

            $this->db->commit();
            return $id;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function todayTotal(): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(total_cost), 0) FROM purchases WHERE purchase_date = CURDATE()'
        );
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    }

    public function totalBetween(string $start, string $end): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(total_cost), 0) FROM purchases 
             WHERE purchase_date BETWEEN :start AND :end'
        );
        $stmt->execute(['start' => $start, 'end' => $end]);
        return (float) $stmt->fetchColumn();
    }

    public function history(?string $date = null): array
    {
        $sql = 'SELECT p.*, i.name AS item_name, i.item_type, u.name AS user_name
                FROM purchases p
                JOIN items i ON i.id = p.item_id
                JOIN users u ON u.id = p.user_id';
        $params = [];

        if ($date !== null) {
            $sql .= ' WHERE p.purchase_date = :date';
            $params['date'] = $date;
        }

        $sql .= ' ORDER BY p.purchase_date DESC, p.id DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function chartData(string $start, string $end): array
    {
        $stmt = $this->db->prepare(
            'SELECT purchase_date AS label, SUM(total_cost) AS total
             FROM purchases
             WHERE purchase_date BETWEEN :start AND :end
             GROUP BY purchase_date
             ORDER BY purchase_date ASC'
        );
        $stmt->execute(['start' => $start, 'end' => $end]);
        return $stmt->fetchAll();
    }
}
