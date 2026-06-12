<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Dashboard extends Model
{
    public function getSummary(): array
    {
        $saleModel = new Sale();
        $purchaseModel = new Purchase();
        $expenseModel = new Expense();

        $todaySales = $saleModel->todayTotal();
        $todayPurchases = $purchaseModel->todayTotal();
        $todayExpenses = $expenseModel->todayTotal();
        $profit = $todaySales - $todayPurchases - $todayExpenses;

        $lowStock = $this->db->query(
            'SELECT * FROM items WHERE is_active = 1 AND current_stock <= 10 ORDER BY current_stock ASC LIMIT 5'
        )->fetchAll();

        return [
            'today_sales' => $todaySales,
            'today_purchases' => $todayPurchases,
            'today_expenses' => $todayExpenses,
            'today_profit' => $profit,
            'low_stock_items' => $lowStock,
        ];
    }

    public function getChartData(int $days = 7): array
    {
        $end = date('Y-m-d');
        $start = date('Y-m-d', strtotime("-{$days} days"));

        $saleModel = new Sale();
        $purchaseModel = new Purchase();
        $expenseModel = new Expense();

        $sales = $this->indexByDate($saleModel->chartData($start, $end));
        $purchases = $this->indexByDate($purchaseModel->chartData($start, $end));
        $expenses = $this->indexByDate($expenseModel->chartData($start, $end));

        $labels = [];
        $salesData = [];
        $purchasesData = [];
        $expensesData = [];

        $current = strtotime($start);
        $endTs = strtotime($end);

        while ($current <= $endTs) {
            $date = date('Y-m-d', $current);
            $labels[] = date('M d', $current);
            $salesData[] = (float) ($sales[$date] ?? 0);
            $purchasesData[] = (float) ($purchases[$date] ?? 0);
            $expensesData[] = (float) ($expenses[$date] ?? 0);
            $current = strtotime('+1 day', $current);
        }

        return [
            'labels' => $labels,
            'sales' => $salesData,
            'purchases' => $purchasesData,
            'expenses' => $expensesData,
        ];
    }

    private function indexByDate(array $rows): array
    {
        $indexed = [];
        foreach ($rows as $row) {
            $indexed[$row['label']] = $row['total'];
        }
        return $indexed;
    }
}
