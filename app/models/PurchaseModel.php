<?php
namespace App\Models;

use Core\Model;

class PurchaseModel extends Model
{
    protected string $table = 'purchases';

    public function getAll(array $filters = []): array
    {
        $sql = "SELECT pu.*, p.name AS product_name FROM purchases pu
                JOIN products p ON pu.product_id = p.id WHERE 1=1";
        $params = [];
        if (!empty($filters['from'])) {
            $sql .= " AND pu.purchase_date >= ?";
            $params[] = $filters['from'];
        }
        if (!empty($filters['to'])) {
            $sql .= " AND pu.purchase_date <= ?";
            $params[] = $filters['to'];
        }
        $sql .= " ORDER BY pu.purchase_date DESC, pu.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $this->db->beginTransaction();
        try {
            $total = $data['quantity'] * $data['buying_cost'];
            $stmt = $this->db->prepare(
                "INSERT INTO purchases (supplier_id, supplier_name, product_id, quantity, buying_cost, total_cost, purchase_date, invoice_number, notes, user_id)
                 VALUES (?,?,?,?,?,?,?,?,?,?)"
            );
            $stmt->execute([
                $data['supplier_id'] ?: null, $data['supplier_name'],
                $data['product_id'], $data['quantity'], $data['buying_cost'], $total,
                $data['purchase_date'], $data['invoice_number'] ?? null,
                $data['notes'] ?? null, $data['user_id'] ?? null,
            ]);
            $id = (int) $this->db->lastInsertId();
            $this->db->prepare("UPDATE products SET quantity = quantity + ?, buying_price = ? WHERE id = ?")
                ->execute([$data['quantity'], $data['buying_cost'], $data['product_id']]);
            $this->db->commit();
            return $id;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data): bool
    {
        $old = $this->find($id);
        if (!$old) return false;
        $this->db->beginTransaction();
        try {
            $diff = $data['quantity'] - $old['quantity'];
            $total = $data['quantity'] * $data['buying_cost'];
            $this->db->prepare(
                "UPDATE purchases SET supplier_id=?, supplier_name=?, product_id=?, quantity=?, buying_cost=?, total_cost=?, purchase_date=?, invoice_number=?, notes=? WHERE id=?"
            )->execute([
                $data['supplier_id'] ?: null, $data['supplier_name'], $data['product_id'],
                $data['quantity'], $data['buying_cost'], $total, $data['purchase_date'],
                $data['invoice_number'] ?? null, $data['notes'] ?? null, $id,
            ]);
            if ($old['product_id'] == $data['product_id']) {
                $this->db->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?")->execute([$diff, $data['product_id']]);
            } else {
                $this->db->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?")->execute([$old['quantity'], $old['product_id']]);
                $this->db->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?")->execute([$data['quantity'], $data['product_id']]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function deletePurchase(int $id): bool
    {
        $old = $this->find($id);
        if (!$old) return false;
        $this->db->beginTransaction();
        try {
            $this->db->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?")->execute([$old['quantity'], $old['product_id']]);
            $this->delete($id);
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function totalPurchases(string $from, string $to): float
    {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(total_cost),0) FROM purchases WHERE purchase_date BETWEEN ? AND ?");
        $stmt->execute([$from, $to]);
        return (float) $stmt->fetchColumn();
    }
}
