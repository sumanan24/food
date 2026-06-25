<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Services\StockService;

class Sale extends Model
{
    public function create(array $data): int
    {
        $this->db->beginTransaction();
        try {
            $itemModel = new Item();
            $item = $itemModel->find((int) $data['item_id']);
            if (!$item) {
                throw new \RuntimeException('Item not found.');
            }

            if ($item['item_type'] === 'long' && (float) $item['current_stock'] < (float) $data['quantity']) {
                throw new \RuntimeException('Insufficient stock for this sale.');
            }

            $stmt = $this->db->prepare(
                'INSERT INTO sales (item_id, user_id, bill_id, quantity, unit_price, total_price, sale_date, notes)
                 VALUES (:item_id, :user_id, :bill_id, :quantity, :unit_price, :total_price, :sale_date, :notes)'
            );
            $stmt->execute([
                'item_id' => $data['item_id'],
                'user_id' => $data['user_id'],
                'bill_id' => $data['bill_id'] ?? null,
                'quantity' => $data['quantity'],
                'unit_price' => $data['unit_price'],
                'total_price' => $data['total_price'],
                'sale_date' => $data['sale_date'],
                'notes' => $data['notes'] ?? null,
            ]);
            $id = (int) $this->db->lastInsertId();

            $stockService = new StockService();
            if (!$stockService->recordSale(
                (int) $data['item_id'],
                (int) $data['user_id'],
                (float) $data['quantity'],
                $id,
                $data['sale_date']
            )) {
                throw new \RuntimeException('Failed to update stock.');
            }

            $this->db->commit();
            return $id;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function createFromBill(array $data): int
    {
        return $this->create($data);
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
        $sql = 'SELECT s.*, i.name AS item_name, i.item_type, u.name AS user_name
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
