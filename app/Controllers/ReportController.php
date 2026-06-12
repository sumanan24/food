<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Report;
use App\Services\PdfReportService;

class ReportController extends Controller
{
    private Report $reportModel;

    public function __construct()
    {
        $this->reportModel = new Report();
        require_once APP_PATH . '/Helpers/functions.php';
    }

    public function index(): void
    {
        $this->view('reports/index', ['title' => 'Reports']);
    }

    public function daily(): void
    {
        $date = (string) ($this->input('date') ?: date('Y-m-d'));
        $report = $this->reportModel->getDaily($date);

        $this->view('reports/show', [
            'title' => 'Daily Report',
            'report' => $report,
            'type' => 'daily',
            'filter' => $date,
        ]);
    }

    public function weekly(): void
    {
        $date = (string) ($this->input('date') ?: date('Y-m-d'));
        $report = $this->reportModel->getWeekly($date);

        $this->view('reports/show', [
            'title' => 'Weekly Report',
            'report' => $report,
            'type' => 'weekly',
            'filter' => $date,
        ]);
    }

    public function monthly(): void
    {
        $date = (string) ($this->input('date') ?: date('Y-m-d'));
        if (preg_match('/^\d{4}-\d{2}$/', $date)) {
            $date .= '-01';
        }
        $report = $this->reportModel->getMonthly($date);

        $this->view('reports/show', [
            'title' => 'Monthly Report',
            'report' => $report,
            'type' => 'monthly',
            'filter' => $date,
        ]);
    }

    public function yearly(): void
    {
        $year = (string) ($this->input('year') ?: date('Y'));
        $report = $this->reportModel->getYearly($year);

        $this->view('reports/show', [
            'title' => 'Yearly Report',
            'report' => $report,
            'type' => 'yearly',
            'filter' => $year,
        ]);
    }

    public function pdf(): void
    {
        $type = (string) $this->input('type', 'daily');
        $filter = (string) ($this->input('filter') ?: date('Y-m-d'));

        $report = match ($type) {
            'weekly' => $this->reportModel->getWeekly($filter),
            'monthly' => $this->reportModel->getMonthly($filter),
            'yearly' => $this->reportModel->getYearly($filter),
            default => $this->reportModel->getDaily($filter),
        };

        $config = require CONFIG_PATH . '/app.php';
        $service = new PdfReportService();
        $service->generate($report, $config);
    }
}
