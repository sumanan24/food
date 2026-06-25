<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\StockLedger;

class InventoryReportController extends Controller
{
    private StockLedger $ledgerModel;

    public function __construct()
    {
        $this->ledgerModel = new StockLedger();
        require_once APP_PATH . '/Helpers/functions.php';
    }

    public function index(): void
    {
        $start = (string) ($this->input('start') ?: date('Y-m-01'));
        $end = (string) ($this->input('end') ?: date('Y-m-d'));

        $this->view('inventory/index', [
            'title' => 'Long Use Inventory',
            'start' => $start,
            'end' => $end,
            'report' => $this->ledgerModel->longUseReport($start, $end),
        ]);
    }

    public function ledger(int $itemId): void
    {
        $start = (string) ($this->input('start') ?: date('Y-m-01'));
        $end = (string) ($this->input('end') ?: date('Y-m-d'));

        $this->view('inventory/ledger', [
            'title' => 'Stock Ledger',
            'itemId' => $itemId,
            'start' => $start,
            'end' => $end,
            'entries' => $this->ledgerModel->forItem($itemId, $start, $end),
        ]);
    }
}
