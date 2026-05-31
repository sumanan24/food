<?php
namespace App\Models;

use Core\Model;

class ProductModel extends Model
{
    protected string $table = 'products';

    public function getAllWithCategory(array $filters = []): array
    {
        $sql = "SELECT p.*, c.name AS category_name FROM products p
                LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.barcode LIKE ?)";
            $like = '%' . $filters['search'] . '%';
            $params[] = $like;
            $params[] = $like;
        }
        if (isset($filters['category_id']) && $filters['category_id'] !== '') {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }
        if (isset($filters['status']) && $filters['status'] !== '') {
            $sql .= " AND p.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['low_stock'])) {
            $threshold = (int)(require dirname(__DIR__, 2) . '/config/app.php')['low_stock_threshold'];
            $sql .= " AND p.quantity <= ?";
            $params[] = $threshold;
        }

        $sql .= " ORDER BY p.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO products (name, category_id, buying_price, selling_price, quantity, barcode, image, expiry_date, status)
             VALUES (?,?,?,?,?,?,?,?,?)"
        );
        $stmt->execute([
            $data['name'], $data['category_id'] ?: null,
            $data['buying_price'], $data['selling_price'], $data['quantity'],
            $data['barcode'] ?: null, $data['image'] ?? null,
            $data['expiry_date'] ?: null, $data['status'] ?? 1,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE products SET name=?, category_id=?, buying_price=?, selling_price=?, quantity=?,
             barcode=?, image=?, expiry_date=?, status=? WHERE id=?"
        );
        return $stmt->execute([
            $data['name'], $data['category_id'] ?: null,
            $data['buying_price'], $data['selling_price'], $data['quantity'],
            $data['barcode'] ?: null, $data['image'] ?? null,
            $data['expiry_date'] ?: null, $data['status'] ?? 1, $id,
        ]);
    }

    public function findByBarcode(string $barcode): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.name AS category_name FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.barcode = ? AND p.status = 1 LIMIT 1"
        );
        $stmt->execute([$barcode]);
        return $stmt->fetch() ?: null;
    }

    public function adjustStock(int $id, int $qty): bool
    {
        $stmt = $this->db->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
        return $stmt->execute([$qty, $id]);
    }

    public function lowStockCount(int $threshold): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE quantity <= ? AND status = 1");
        $stmt->execute([$threshold]);
        return (int) $stmt->fetchColumn();
    }

    public function topSelling(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT si.product_id, si.product_name, SUM(si.quantity) AS total_qty, SUM(si.line_total) AS total_sales
             FROM sale_items si GROUP BY si.product_id, si.product_name
             ORDER BY total_qty DESC LIMIT ?"
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
