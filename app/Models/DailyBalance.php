<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class DailyBalance extends Model
{
    public function getReport(string $date): array
    {
        $items = $this->db->query(
            "SELECT * FROM items WHERE is_active = 1 AND item_type = 'daily' ORDER BY name ASC"
        )->fetchAll();

        $report = [];
        foreach ($items as $item) {
            $itemId = (int) $item['id'];
            $opening = $this->getOpening($itemId, $date);
            $purchased = $this->getPurchased($itemId, $date);
            $sold = $this->getSold($itemId, $date);
            $wastage = $this->getWastage($itemId, $date);
            $balance = $opening + $purchased - $sold - $wastage;
            $profit = ($sold * (float) $item['selling_price']) - ($sold * (float) $item['cost_price']);

            $report[] = [
                'item_id' => $itemId,
                'item_name' => $item['name'],
                'cost_price' => (float) $item['cost_price'],
                'selling_price' => (float) $item['selling_price'],
                'opening_qty' => $opening,
                'purchased_qty' => $purchased,
                'sold_qty' => $sold,
                'wastage_qty' => $wastage,
                'balance_qty' => $balance,
                'daily_profit' => $profit,
            ];
        }

        return $report;
    }

    public function setOpening(int $itemId, int $userId, float $qty, string $date): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO daily_openings (balance_date, item_id, opening_qty, user_id)
             VALUES (:date, :item_id, :qty, :user_id)
             ON DUPLICATE KEY UPDATE opening_qty = :qty2, user_id = :user_id2'
        );
        $stmt->execute([
            'date' => $date,
            'item_id' => $itemId,
            'qty' => $qty,
            'user_id' => $userId,
            'qty2' => $qty,
            'user_id2' => $userId,
        ]);
    }

    private function getOpening(int $itemId, string $date): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(opening_qty, 0) FROM daily_openings WHERE item_id = :item_id AND balance_date = :date'
        );
        $stmt->execute(['item_id' => $itemId, 'date' => $date]);
        return (float) $stmt->fetchColumn();
    }

    private function getPurchased(int $itemId, string $date): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(quantity), 0) FROM purchases WHERE item_id = :item_id AND purchase_date = :date'
        );
        $stmt->execute(['item_id' => $itemId, 'date' => $date]);
        return (float) $stmt->fetchColumn();
    }

    private function getSold(int $itemId, string $date): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(quantity), 0) FROM sales WHERE item_id = :item_id AND sale_date = :date'
        );
        $stmt->execute(['item_id' => $itemId, 'date' => $date]);
        return (float) $stmt->fetchColumn();
    }

    private function getWastage(int $itemId, string $date): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(quantity), 0) FROM wastage WHERE item_id = :item_id AND wastage_date = :date'
        );
        $stmt->execute(['item_id' => $itemId, 'date' => $date]);
        return (float) $stmt->fetchColumn();
    }
}
