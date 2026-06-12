<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Sale extends Model
{
    public function create(array $data): int
    {
        $this->db->beginTransaction();
        try {
            $itemModel = new Item();
            $item = $itemModel->find((int) $data['item_id']);
            if (!$item || (float) $item['current_stock'] < (float) $data['quantity']) {
                throw new \RuntimeException('Insufficient stock for this sale.');
            }

            $stmt = $this->db->prepare(
                'INSERT INTO sales (item_id, user_id, quantity, unit_price, total_price, sale_date, notes)
                 VALUES (:item_id, :user_id, :quantity, :unit_price, :total_price, :sale_date, :notes)'
            );
            $stmt->execute([
                'item_id' => $data['item_id'],
                'user_id' => $data['user_id'],
                'quantity' => $data['quantity'],
                'unit_price' => $data['unit_price'],
                'total_price' => $data['total_price'],
                'sale_date' => $data['sale_date'],
                'notes' => $data['notes'] ?? null,
            ]);
            $id = (int) $this->db->lastInsertId();

            if (!$itemModel->decreaseStock((int) $data['item_id'], (float) $data['quantity'])) {
                throw new \RuntimeException('Failed to update stock.');
            }

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
            'SELECT COALESCE(SUM(total_price), 0) FROM sales WHERE sale_date = CURDATE()'
        );
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    }

    public function totalBetween(string $start, string $end): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(total_price), 0) FROM sales 
             WHERE sale_date BETWEEN :start AND :end'
        );
        $stmt->execute(['start' => $start, 'end' => $end]);
        return (float) $stmt->fetchColumn();
    }

    public function history(?string $date = null): array
    {
        $sql = 'SELECT s.*, i.name AS item_name, u.name AS user_name
                FROM sales s
                JOIN items i ON i.id = s.item_id
                JOIN users u ON u.id = s.user_id';
        $params = [];

        if ($date !== null) {
            $sql .= ' WHERE s.sale_date = :date';
            $params['date'] = $date;
        }

        $sql .= ' ORDER BY s.sale_date DESC, s.id DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function chartData(string $start, string $end): array
    {
        $stmt = $this->db->prepare(
            'SELECT sale_date AS label, SUM(total_price) AS total
             FROM sales
             WHERE sale_date BETWEEN :start AND :end
             GROUP BY sale_date
             ORDER BY sale_date ASC'
        );
        $stmt->execute(['start' => $start, 'end' => $end]);
        return $stmt->fetchAll();
    }
}
