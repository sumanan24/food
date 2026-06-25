<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class StockLedger extends Model
{
    public function add(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO stock_ledger (item_id, user_id, transaction_type, reference_id,
             quantity_in, quantity_out, balance_after, ledger_date, notes)
             VALUES (:item_id, :user_id, :transaction_type, :reference_id,
             :quantity_in, :quantity_out, :balance_after, :ledger_date, :notes)'
        );
        $stmt->execute([
            'item_id' => $data['item_id'],
            'user_id' => $data['user_id'],
            'transaction_type' => $data['transaction_type'],
            'reference_id' => $data['reference_id'] ?? null,
            'quantity_in' => $data['quantity_in'],
            'quantity_out' => $data['quantity_out'],
            'balance_after' => $data['balance_after'],
            'ledger_date' => $data['ledger_date'],
            'notes' => $data['notes'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function forItem(int $itemId, ?string $start = null, ?string $end = null): array
    {
        $sql = 'SELECT sl.*, u.name AS user_name FROM stock_ledger sl
                JOIN users u ON u.id = sl.user_id
                WHERE sl.item_id = :item_id';
        $params = ['item_id' => $itemId];
        if ($start && $end) {
            $sql .= ' AND sl.ledger_date BETWEEN :start AND :end';
            $params['start'] = $start;
            $params['end'] = $end;
        }
        $sql .= ' ORDER BY sl.ledger_date DESC, sl.id DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function longUseReport(string $start, string $end): array
    {
        $items = $this->db->query(
            "SELECT * FROM items WHERE is_active = 1 AND item_type = 'long' ORDER BY name ASC"
        )->fetchAll();

        $report = [];
        foreach ($items as $item) {
            $itemId = (int) $item['id'];
            $opening = $this->getOpeningStock($itemId, $start);
            $purchased = $this->sumPurchased($itemId, $start, $end);
            $sold = $this->sumSold($itemId, $start, $end);
            $wastage = $this->sumWastage($itemId, $start, $end);
            $currentStock = (float) $item['current_stock'];
            $valuation = $currentStock * (float) $item['cost_price'];

            $report[] = [
                'item_id' => $itemId,
                'item_name' => $item['name'],
                'reorder_level' => (float) $item['reorder_level'],
                'opening_stock' => $opening,
                'purchased' => $purchased,
                'sold' => $sold,
                'wastage' => $wastage,
                'current_stock' => $currentStock,
                'stock_valuation' => $valuation,
                'low_stock' => $currentStock <= (float) $item['reorder_level'],
            ];
        }

        return $report;
    }

    private function getOpeningStock(int $itemId, string $date): float
    {
        $stmt = $this->db->prepare(
            'SELECT balance_after FROM stock_ledger
             WHERE item_id = :item_id AND ledger_date < :date
             ORDER BY ledger_date DESC, id DESC LIMIT 1'
        );
        $stmt->execute(['item_id' => $itemId, 'date' => $date]);
        $val = $stmt->fetchColumn();
        if ($val !== false) {
            return (float) $val;
        }
        $item = (new Item())->find($itemId);
        return $item ? (float) $item['current_stock'] : 0;
    }

    private function sumPurchased(int $itemId, string $start, string $end): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(quantity), 0) FROM purchases
             WHERE item_id = :item_id AND purchase_date BETWEEN :start AND :end'
        );
        $stmt->execute(['item_id' => $itemId, 'start' => $start, 'end' => $end]);
        return (float) $stmt->fetchColumn();
    }

    private function sumSold(int $itemId, string $start, string $end): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(quantity), 0) FROM sales
             WHERE item_id = :item_id AND sale_date BETWEEN :start AND :end'
        );
        $stmt->execute(['item_id' => $itemId, 'start' => $start, 'end' => $end]);
        return (float) $stmt->fetchColumn();
    }

    private function sumWastage(int $itemId, string $start, string $end): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(quantity), 0) FROM wastage
             WHERE item_id = :item_id AND wastage_date BETWEEN :start AND :end'
        );
        $stmt->execute(['item_id' => $itemId, 'start' => $start, 'end' => $end]);
        return (float) $stmt->fetchColumn();
    }
}
