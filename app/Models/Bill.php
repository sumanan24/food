<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Bill extends Model
{
    public function generateBillNumber(string $date): string
    {
        $prefix = 'BILL-' . str_replace('-', '', $date) . '-';
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM bills WHERE bill_date = :date'
        );
        $stmt->execute(['date' => $date]);
        $seq = (int) $stmt->fetchColumn() + 1;
        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    public function createWithItems(array $billData, array $items): int
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO bills (bill_number, user_id, cash_session_id, subtotal, discount, total_amount, payment_method, bill_date, notes)
                 VALUES (:bill_number, :user_id, :cash_session_id, :subtotal, :discount, :total_amount, :payment_method, :bill_date, :notes)'
            );
            $stmt->execute([
                'bill_number' => $billData['bill_number'],
                'user_id' => $billData['user_id'],
                'cash_session_id' => $billData['cash_session_id'] ?? null,
                'subtotal' => $billData['subtotal'],
                'discount' => $billData['discount'],
                'total_amount' => $billData['total_amount'],
                'payment_method' => $billData['payment_method'],
                'bill_date' => $billData['bill_date'],
                'notes' => $billData['notes'] ?? null,
            ]);
            $billId = (int) $this->db->lastInsertId();

            $itemStmt = $this->db->prepare(
                'INSERT INTO bill_items (bill_id, item_id, quantity, unit_price, total_price)
                 VALUES (:bill_id, :item_id, :quantity, :unit_price, :total_price)'
            );
            $saleModel = new Sale();
            foreach ($items as $line) {
                $itemStmt->execute([
                    'bill_id' => $billId,
                    'item_id' => $line['item_id'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'total_price' => $line['total_price'],
                ]);
                $saleModel->createFromBill([
                    'item_id' => $line['item_id'],
                    'user_id' => $billData['user_id'],
                    'bill_id' => $billId,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'total_price' => $line['total_price'],
                    'sale_date' => $billData['bill_date'],
                ]);
            }

            $this->db->commit();
            return $billId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT b.*, u.name AS user_name FROM bills b
             JOIN users u ON u.id = b.user_id WHERE b.id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getItems(int $billId): array
    {
        $stmt = $this->db->prepare(
            'SELECT bi.*, i.name AS item_name FROM bill_items bi
             JOIN items i ON i.id = bi.item_id WHERE bi.bill_id = :bill_id'
        );
        $stmt->execute(['bill_id' => $billId]);
        return $stmt->fetchAll();
    }

    public function todayTotal(): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(total_amount), 0) FROM bills WHERE bill_date = CURDATE()'
        );
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    }

    public function totalForSession(int $sessionId): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(total_amount), 0) FROM bills WHERE cash_session_id = :session_id'
        );
        $stmt->execute(['session_id' => $sessionId]);
        return (float) $stmt->fetchColumn();
    }

    public function totalBetween(string $start, string $end): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(total_amount), 0) FROM bills WHERE bill_date BETWEEN :start AND :end'
        );
        $stmt->execute(['start' => $start, 'end' => $end]);
        return (float) $stmt->fetchColumn();
    }

    public function history(?string $date = null): array
    {
        $sql = 'SELECT b.*, u.name AS user_name FROM bills b
                JOIN users u ON u.id = b.user_id';
        $params = [];
        if ($date !== null) {
            $sql .= ' WHERE b.bill_date = :date';
            $params['date'] = $date;
        }
        $sql .= ' ORDER BY b.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
