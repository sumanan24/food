<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Item;
use App\Models\Sale;

class SaleController extends Controller
{
    private Sale $saleModel;
    private Item $itemModel;

    public function __construct()
    {
        $this->saleModel = new Sale();
        $this->itemModel = new Item();
        require_once APP_PATH . '/Helpers/functions.php';
    }

    public function index(): void
    {
        $today = date('Y-m-d');
        $this->view('sales/index', [
            'title' => "Today's Sales",
            'sales' => $this->saleModel->history($today),
            'date' => $today,
        ]);
    }

    public function create(): void
    {
        $this->view('sales/create', [
            'title' => 'Record Sale',
            'items' => $this->itemModel->all(),
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();

        $itemId = (int) $this->input('item_id', 0);
        $quantity = (float) $this->input('quantity', 0);
        $unitPrice = (float) $this->input('unit_price', 0);
        $saleDate = (string) $this->input('sale_date', date('Y-m-d'));
        $notes = trim((string) $this->input('notes', ''));

        if ($itemId <= 0 || $quantity <= 0 || $unitPrice < 0) {
            Session::flash('error', 'Please provide valid sale details.');
            $this->redirect('/sales/create');
        }

        $item = $this->itemModel->find($itemId);
        if (!$item) {
            Session::flash('error', 'Selected item not found.');
            $this->redirect('/sales/create');
        }

        if ((float) $item['current_stock'] < $quantity) {
            Session::flash('error', 'Insufficient stock. Available: ' . $item['current_stock']);
            $this->redirect('/sales/create');
        }

        try {
            $this->saleModel->create([
                'item_id' => $itemId,
                'user_id' => Auth::id(),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $quantity * $unitPrice,
                'sale_date' => $saleDate,
                'notes' => $notes ?: null,
            ]);
            Session::flash('success', 'Sale recorded and stock updated.');
        } catch (\Throwable $e) {
            Session::flash('error', 'Failed to record sale: ' . $e->getMessage());
        }

        $this->redirect('/sales');
    }

    public function history(): void
    {
        $date = $this->input('date');
        $this->view('sales/history', [
            'title' => 'Sales History',
            'sales' => $this->saleModel->history($date ?: null),
            'filterDate' => $date,
        ]);
    }
}
