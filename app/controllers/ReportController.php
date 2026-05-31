<?php
namespace App\Controllers;

use App\Models\ExpenseModel;
use App\Models\ProductModel;
use App\Models\PurchaseModel;
use App\Models\ReportModel;
use App\Models\SaleModel;
use Core\Controller;

class ReportController extends Controller
{
    private ReportModel $reports;

    public function __construct()
    {
        $this->reports = new ReportModel();
    }

    public function index(): void
    {
        $this->requireAuth();
        $this->view('reports.index', ['title' => 'Reports']);
    }

    public function profit(): void
    {
        $this->requireAuth();
        [$from, $to, $period] = $this->dateRange();
        $data = $this->reports->profit($from, $to);
        $this->view('reports.profit', compact('from', 'to', 'period', 'data') + ['title' => 'Profit Report']);
    }

    public function sales(): void
    {
        $this->requireAuth();
        [$from, $to, $period] = $this->dateRange();
        $sales = (new SaleModel())->history(['from' => $from, 'to' => $to]);
        $total = (new SaleModel())->totalSales($from, $to);
        $this->view('reports.sales', compact('from', 'to', 'period', 'sales', 'total') + ['title' => 'Sales Report']);
    }

    public function expenses(): void
    {
        $this->requireAuth();
        [$from, $to, $period] = $this->dateRange();
        $expenses = (new ExpenseModel())->getAll(['from' => $from, 'to' => $to]);
        $total = (new ExpenseModel())->totalExpenses($from, $to);
        $this->view('reports.expenses', compact('from', 'to', 'period', 'expenses', 'total') + ['title' => 'Expense Report']);
    }

    public function purchases(): void
    {
        $this->requireAuth();
        [$from, $to, $period] = $this->dateRange();
        $purchases = (new PurchaseModel())->getAll(['from' => $from, 'to' => $to]);
        $total = (new PurchaseModel())->totalPurchases($from, $to);
        $this->view('reports.purchases', compact('from', 'to', 'period', 'purchases', 'total') + ['title' => 'Purchase Report']);
    }

    public function stock(): void
    {
        $this->requireAuth();
        $products = (new ProductModel())->getAllWithCategory();
        $this->view('reports.stock', ['title' => 'Stock Report', 'products' => $products]);
    }

    public function export(): void
    {
        $this->requireAuth();
        $type = $_GET['type'] ?? 'profit';
        $format = $_GET['format'] ?? 'csv';
        [$from, $to] = $this->dateRange();

        if ($format === 'csv' || $format === 'excel') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="report_' . $type . '_' . date('Ymd') . '.csv"');
            $out = fopen('php://output', 'w');

            if ($type === 'profit') {
                $d = $this->reports->profit($from, $to);
                fputcsv($out, ['Metric', 'Amount']);
                fputcsv($out, ['Total Sales', $d['total_sales']]);
                fputcsv($out, ['Product Cost', $d['product_cost']]);
                fputcsv($out, ['Expenses', $d['expenses']]);
                fputcsv($out, ['Profit', $d['profit']]);
            } elseif ($type === 'sales') {
                fputcsv($out, ['Invoice', 'Date', 'Customer', 'Total', 'Payment']);
                foreach ((new SaleModel())->history(['from' => $from, 'to' => $to]) as $s) {
                    fputcsv($out, [$s['invoice_number'], $s['sale_date'], $s['customer_name'], $s['grand_total'], $s['payment_type']]);
                }
            }
            fclose($out);
            exit;
        }

        // Simple HTML print as PDF alternative
        redirect('reports/' . $type . '?from=' . $from . '&to=' . $to . '&print=1');
    }

    private function dateRange(): array
    {
        $period = $_GET['period'] ?? 'daily';
        $from = $_GET['from'] ?? '';
        $to = $_GET['to'] ?? date('Y-m-d');

        if (!$from) {
            switch ($period) {
                case 'weekly':
                    $from = date('Y-m-d', strtotime('monday this week'));
                    break;
                case 'monthly':
                    $from = date('Y-m-01');
                    break;
                case 'yearly':
                    $from = date('Y-01-01');
                    break;
                default:
                    $from = date('Y-m-d');
            }
        }
        return [$from, $to, $period];
    }
}
