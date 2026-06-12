<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Dashboard;

class DashboardController extends Controller
{
    public function __construct()
    {
        require_once APP_PATH . '/Helpers/functions.php';
    }

    public function index(): void
    {
        $dashboard = new Dashboard();
        $summary = $dashboard->getSummary();
        $charts = $dashboard->getChartData(7);
        $config = require CONFIG_PATH . '/app.php';

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'summary' => $summary,
            'charts' => $charts,
            'config' => $config,
        ]);
    }
}
