<?php
namespace App\Controllers;

use App\Models\ExpenseModel;
use App\Models\ProductModel;
use App\Models\PurchaseModel;
use App\Models\ReportModel;
use App\Models\SaleModel;
use App\Models\SettingModel;
use Core\Controller;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $config = require dirname(__DIR__, 2) . '/config/app.php';
        $today = date('Y-m-d');

        $saleModel = new SaleModel();
        $expenseModel = new ExpenseModel();
        $purchaseModel = new PurchaseModel();
        $productModel = new ProductModel();
        $reportModel = new ReportModel();

        $todaySales = $saleModel->totalSales($today, $today);
        $todayExpenses = $expenseModel->totalExpenses($today, $today);
        $todayPurchases = $purchaseModel->totalPurchases($today, $today);
        $profitData = $reportModel->profit($today, $today);

        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $monthStart = date('Y-m-01');
        $yearStart = date('Y-01-01');

        $this->view('dashboard.index', [
            'title'           => 'Dashboard',
            'todaySales'      => $todaySales,
            'todayExpenses'   => $todayExpenses,
            'todayPurchases'  => $todayPurchases,
            'todayProfit'     => $profitData['profit'],
            'totalProducts'   => $productModel->count(),
            'lowStock'        => $productModel->lowStockCount($config['low_stock_threshold']),
            'weeklyIncome'    => $saleModel->totalSales($weekStart, $today),
            'monthlyIncome'   => $saleModel->totalSales($monthStart, $today),
            'yearlyIncome'    => $saleModel->totalSales($yearStart, $today),
            'topProducts'     => $productModel->topSelling(5),
            'settings'        => (new SettingModel())->getAll(),
        ]);
    }

    public function stats(): void
    {
        $this->requireAuth();
        $report = new ReportModel();
        $period = $_GET['period'] ?? 'weekly';
        $this->json(['success' => true, 'chart' => $report->chartData($period)]);
    }
}
