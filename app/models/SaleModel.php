<?php
namespace App\Models;

use Core\Model;
use PDO;

class SaleModel extends Model
{
    protected string $table = 'sales';

    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $row = $this->db->query("SELECT setting_value FROM settings WHERE setting_key = 'invoice_prefix'")->fetch();
        if ($row) $prefix = $row['setting_value'];
        $date = date('Ymd');
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM sales WHERE DATE(created_at) = CURDATE()");
        $stmt->execute();
        $count = (int) $stmt->fetchColumn() + 1;
        return $prefix . '-' . $date . '-' . str_pad((string)$count, 4, '0', STR_PAD_LEFT);
    }

    public function createSale(array $sale, array $items): int
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO sales (invoice_number, sale_date, customer_name, subtotal, discount, tax, grand_total,
                 payment_type, paid_amount, change_amount, cost_total, notes, user_id)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)"
            );
            $stmt->execute([
                $sale['invoice_number'], $sale['sale_date'], $sale['customer_name'] ?? null,
                $sale['subtotal'], $sale['discount'], $sale['tax'], $sale['grand_total'],
                $sale['payment_type'], $sale['paid_amount'], $sale['change_amount'],
                $sale['cost_total'], $sale['notes'] ?? null, $sale['user_id'] ?? null,
            ]);
            $saleId = (int) $this->db->lastInsertId();

            $itemStmt = $this->db->prepare(
                "INSERT INTO sale_items (sale_id, product_id, product_name, quantity, unit_price, buying_price, line_total)
                 VALUES (?,?,?,?,?,?,?)"
            );
            $stockStmt = $this->db->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ? AND quantity >= ?");

            foreach ($items as $item) {
                $itemStmt->execute([
                    $saleId, $item['product_id'], $item['product_name'],
                    $item['quantity'], $item['unit_price'], $item['buying_price'], $item['line_total'],
                ]);
                $stockStmt->execute([$item['quantity'], $item['product_id'], $item['quantity']]);
                if ($stockStmt->rowCount() === 0) {
                    throw new \Exception('Insufficient stock for ' . $item['product_name']);
                }
            }

            $this->db->commit();
            return $saleId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getWithItems(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM sales WHERE id = ?");
        $stmt->execute([$id]);
        $sale = $stmt->fetch();
        if (!$sale) return null;
        $items = $this->db->prepare("SELECT * FROM sale_items WHERE sale_id = ?");
        $items->execute([$id]);
        $sale['items'] = $items->fetchAll();
        return $sale;
    }

    public function history(array $filters = []): array
    {
        $sql = "SELECT s.*, u.name AS user_name FROM sales s LEFT JOIN users u ON s.user_id = u.id WHERE 1=1";
        $params = [];
        if (!empty($filters['from'])) {
            $sql .= " AND s.sale_date >= ?";
            $params[] = $filters['from'];
        }
        if (!empty($filters['to'])) {
            $sql .= " AND s.sale_date <= ?";
            $params[] = $filters['to'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (s.invoice_number LIKE ? OR s.customer_name LIKE ?)";
            $like = '%' . $filters['search'] . '%';
            $params[] = $like;
            $params[] = $like;
        }
        $sql .= " ORDER BY s.created_at DESC LIMIT 500";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function totalSales(string $from, string $to): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(grand_total),0) FROM sales WHERE sale_date BETWEEN ? AND ?"
        );
        $stmt->execute([$from, $to]);
        return (float) $stmt->fetchColumn();
    }

    public function totalCost(string $from, string $to): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(cost_total),0) FROM sales WHERE sale_date BETWEEN ? AND ?"
        );
        $stmt->execute([$from, $to]);
        return (float) $stmt->fetchColumn();
    }
}
