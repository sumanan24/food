<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Dashboard extends Model
{
    public function getSummary(): array
    {
        $billModel = new Bill();
        $purchaseModel = new Purchase();
        $expenseModel = new Expense();
        $cashModel = new CashSession();

        $todaySales = $billModel->todayTotal();
        $todayPurchases = $purchaseModel->todayTotal();
        $todayExpenses = $expenseModel->todayTotal();
        $profit = $todaySales - $todayPurchases - $todayExpenses;

        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-d');
        $monthlySales = $billModel->totalBetween($monthStart, $monthEnd);
        $monthlyPurchases = $purchaseModel->totalBetween($monthStart, $monthEnd);
        $monthlyExpenses = $expenseModel->totalBetween($monthStart, $monthEnd);
        $monthlyProfit = $monthlySales - $monthlyPurchases - $monthlyExpenses;

        $cashSession = $cashModel->getOpenSession();
        if ($cashSession) {
            $sessionSales = $billModel->totalForSession((int) $cashSession['id']);
            $sessionExpenses = $expenseModel->totalDuringSession($cashSession['opened_at']);
            $cashInHand = (float) $cashSession['opening_balance'] + $sessionSales - $sessionExpenses;
        } else {
            $cashInHand = 0;
        }

        $itemModel = new Item();
        $lowStock = $itemModel->lowStockItems();

        return [
            'today_sales' => $todaySales,
            'today_purchases' => $todayPurchases,
            'today_expenses' => $todayExpenses,
            'today_profit' => $profit,
            'monthly_profit' => $monthlyProfit,
            'cash_in_hand' => $cashInHand,
            'cash_session' => $cashSession,
            'low_stock_items' => $lowStock,
        ];
    }

    public function getChartData(int $days = 7): array
    {
        $end = date('Y-m-d');
        $start = date('Y-m-d', strtotime("-{$days} days"));

        $billModel = new Bill();
        $purchaseModel = new Purchase();
        $expenseModel = new Expense();

        $sales = $this->indexByDate($this->billChartData($start, $end));
        $purchases = $this->indexByDate($purchaseModel->chartData($start, $end));
        $expenses = $this->indexByDate($expenseModel->chartData($start, $end));

        $labels = [];
        $salesData = [];
        $purchasesData = [];
        $expensesData = [];
        $profitData = [];

        $current = strtotime($start);
        $endTs = strtotime($end);

        while ($current <= $endTs) {
            $date = date('Y-m-d', $current);
            $labels[] = date('M d', $current);
            $s = (float) ($sales[$date] ?? 0);
            $p = (float) ($purchases[$date] ?? 0);
            $e = (float) ($expenses[$date] ?? 0);
            $salesData[] = $s;
            $purchasesData[] = $p;
            $expensesData[] = $e;
            $profitData[] = $s - $p - $e;
            $current = strtotime('+1 day', $current);
        }

        return [
            'labels' => $labels,
            'sales' => $salesData,
            'purchases' => $purchasesData,
            'expenses' => $expensesData,
            'profit' => $profitData,
        ];
    }

    private function billChartData(string $start, string $end): array
    {
        $stmt = $this->db->prepare(
            'SELECT bill_date AS label, SUM(total_amount) AS total
             FROM bills WHERE bill_date BETWEEN :start AND :end
             GROUP BY bill_date ORDER BY bill_date ASC'
        );
        $stmt->execute(['start' => $start, 'end' => $end]);
        return $stmt->fetchAll();
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
