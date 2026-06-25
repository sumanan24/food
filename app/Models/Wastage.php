<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Services\StockService;

class Wastage extends Model
{
    public function create(array $data): int
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO wastage (item_id, user_id, quantity, wastage_date, notes)
                 VALUES (:item_id, :user_id, :quantity, :wastage_date, :notes)'
            );
            $stmt->execute([
                'item_id' => $data['item_id'],
                'user_id' => $data['user_id'],
                'quantity' => $data['quantity'],
                'wastage_date' => $data['wastage_date'],
                'notes' => $data['notes'] ?? null,
            ]);
            $id = (int) $this->db->lastInsertId();

            $stockService = new StockService();
            $stockService->recordWastage(
                (int) $data['item_id'],
                (int) $data['user_id'],
                (float) $data['quantity'],
                $id,
                $data['wastage_date']
            );

            $this->db->commit();
            return $id;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function history(?string $date = null): array
    {
        $sql = 'SELECT w.*, i.name AS item_name, u.name AS user_name
                FROM wastage w
                JOIN items i ON i.id = w.item_id
                JOIN users u ON u.id = w.user_id';
        $params = [];
        if ($date !== null) {
            $sql .= ' WHERE w.wastage_date = :date';
            $params['date'] = $date;
        }
        $sql .= ' ORDER BY w.wastage_date DESC, w.id DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
