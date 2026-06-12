<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Report extends Model
{
    public function getDaily(string $date): array
    {
        return $this->buildReport($date, $date, 'Daily Report - ' . $date);
    }

    public function getWeekly(?string $date = null): array
    {
        $date = $date ?? date('Y-m-d');
        $start = date('Y-m-d', strtotime('monday this week', strtotime($date)));
        $end = date('Y-m-d', strtotime('sunday this week', strtotime($date)));
        return $this->buildReport($start, $end, "Weekly Report ({$start} to {$end})");
    }

    public function getMonthly(?string $date = null): array
    {
        $date = $date ?? date('Y-m-d');
        $start = date('Y-m-01', strtotime($date));
        $end = date('Y-m-t', strtotime($date));
        return $this->buildReport($start, $end, 'Monthly Report - ' . date('F Y', strtotime($date)));
    }

    public function getYearly(?string $year = null): array
    {
        $year = $year ?? date('Y');
        $start = "{$year}-01-01";
        $end = "{$year}-12-31";
        return $this->buildReport($start, $end, "Yearly Report - {$year}");
    }

    private function buildReport(string $start, string $end, string $title): array
    {
        $saleModel = new Sale();
        $purchaseModel = new Purchase();
        $expenseModel = new Expense();

        $totalSales = $saleModel->totalBetween($start, $end);
        $totalPurchases = $purchaseModel->totalBetween($start, $end);
        $totalExpenses = $expenseModel->totalBetween($start, $end);
        $profit = $totalSales - $totalPurchases - $totalExpenses;

        $sales = $this->fetchSales($start, $end);
        $purchases = $this->fetchPurchases($start, $end);
        $expenses = $expenseModel->between($start, $end);

        return [
            'title' => $title,
            'start' => $start,
            'end' => $end,
            'total_sales' => $totalSales,
            'total_purchases' => $totalPurchases,
            'total_expenses' => $totalExpenses,
            'profit' => $profit,
            'sales' => $sales,
            'purchases' => $purchases,
            'expenses' => $expenses,
        ];
    }

    private function fetchSales(string $start, string $end): array
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, i.name AS item_name, u.name AS user_name
             FROM sales s
             JOIN items i ON i.id = s.item_id
             JOIN users u ON u.id = s.user_id
             WHERE s.sale_date BETWEEN :start AND :end
             ORDER BY s.sale_date DESC'
        );
        $stmt->execute(['start' => $start, 'end' => $end]);
        return $stmt->fetchAll();
    }

    private function fetchPurchases(string $start, string $end): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, i.name AS item_name, u.name AS user_name
             FROM purchases p
             JOIN items i ON i.id = p.item_id
             JOIN users u ON u.id = p.user_id
             WHERE p.purchase_date BETWEEN :start AND :end
             ORDER BY p.purchase_date DESC'
        );
        $stmt->execute(['start' => $start, 'end' => $end]);
        return $stmt->fetchAll();
    }
}
