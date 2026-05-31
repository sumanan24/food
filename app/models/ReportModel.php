<?php
namespace App\Models;

use Core\Database;
use PDO;

class ReportModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function profit(string $from, string $to): array
    {
        $sales = $this->db->prepare("SELECT COALESCE(SUM(grand_total),0) AS total FROM sales WHERE sale_date BETWEEN ? AND ?");
        $sales->execute([$from, $to]);
        $totalSales = (float) $sales->fetchColumn();

        $cost = $this->db->prepare("SELECT COALESCE(SUM(cost_total),0) AS total FROM sales WHERE sale_date BETWEEN ? AND ?");
        $cost->execute([$from, $to]);
        $productCost = (float) $cost->fetchColumn();

        $exp = $this->db->prepare("SELECT COALESCE(SUM(amount),0) AS total FROM expenses WHERE expense_date BETWEEN ? AND ?");
        $exp->execute([$from, $to]);
        $expenses = (float) $exp->fetchColumn();

        $profit = $totalSales - $productCost - $expenses;

        return [
            'total_sales'   => $totalSales,
            'product_cost'  => $productCost,
            'expenses'      => $expenses,
            'profit'        => $profit,
        ];
    }

    public function chartData(string $period): array
    {
        $labels = [];
        $sales = [];
        $expenses = [];

        if ($period === 'weekly') {
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $labels[] = date('D', strtotime($date));
                $sales[] = $this->daySales($date);
                $expenses[] = $this->dayExpenses($date);
            }
        } elseif ($period === 'monthly') {
            $days = (int) date('t');
            for ($d = 1; $d <= $days; $d += max(1, (int)($days / 10))) {
                $date = date('Y-m-') . str_pad((string)$d, 2, '0', STR_PAD_LEFT);
                $labels[] = (string)$d;
                $sales[] = $this->daySales($date);
                $expenses[] = $this->dayExpenses($date);
            }
        } else {
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = date('M', mktime(0, 0, 0, $m, 1));
                $from = date('Y') . '-' . str_pad((string)$m, 2, '0', STR_PAD_LEFT) . '-01';
                $to = date('Y-m-t', strtotime($from));
                $sales[] = $this->rangeSales($from, $to);
                $expenses[] = $this->rangeExpenses($from, $to);
            }
        }

        return compact('labels', 'sales', 'expenses');
    }

    private function daySales(string $date): float
    {
        $s = $this->db->prepare("SELECT COALESCE(SUM(grand_total),0) FROM sales WHERE sale_date = ?");
        $s->execute([$date]);
        return (float) $s->fetchColumn();
    }

    private function dayExpenses(string $date): float
    {
        $s = $this->db->prepare("SELECT COALESCE(SUM(amount),0) FROM expenses WHERE expense_date = ?");
        $s->execute([$date]);
        return (float) $s->fetchColumn();
    }

    private function rangeSales(string $from, string $to): float
    {
        $s = $this->db->prepare("SELECT COALESCE(SUM(grand_total),0) FROM sales WHERE sale_date BETWEEN ? AND ?");
        $s->execute([$from, $to]);
        return (float) $s->fetchColumn();
    }

    private function rangeExpenses(string $from, string $to): float
    {
        $s = $this->db->prepare("SELECT COALESCE(SUM(amount),0) FROM expenses WHERE expense_date BETWEEN ? AND ?");
        $s->execute([$from, $to]);
        return (float) $s->fetchColumn();
    }
}
