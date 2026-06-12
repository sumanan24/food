<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Item;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    private Purchase $purchaseModel;
    private Item $itemModel;

    public function __construct()
    {
        $this->purchaseModel = new Purchase();
        $this->itemModel = new Item();
        require_once APP_PATH . '/Helpers/functions.php';
    }

    public function index(): void
    {
        $today = date('Y-m-d');
        $this->view('purchases/index', [
            'title' => "Today's Purchases",
            'purchases' => $this->purchaseModel->history($today),
            'date' => $today,
        ]);
    }

    public function create(): void
    {
        $this->view('purchases/create', [
            'title' => 'Record Purchase',
            'items' => $this->itemModel->all(),
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();

        $itemId = (int) $this->input('item_id', 0);
        $quantity = (float) $this->input('quantity', 0);
        $unitCost = (float) $this->input('unit_cost', 0);
        $purchaseDate = (string) $this->input('purchase_date', date('Y-m-d'));
        $notes = trim((string) $this->input('notes', ''));

        if ($itemId <= 0 || $quantity <= 0 || $unitCost < 0) {
            Session::flash('error', 'Please provide valid purchase details.');
            $this->redirect('/purchases/create');
        }

        $item = $this->itemModel->find($itemId);
        if (!$item) {
            Session::flash('error', 'Selected item not found.');
            $this->redirect('/purchases/create');
        }

        try {
            $this->purchaseModel->create([
                'item_id' => $itemId,
                'user_id' => Auth::id(),
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $quantity * $unitCost,
                'purchase_date' => $purchaseDate,
                'notes' => $notes ?: null,
            ]);
            Session::flash('success', 'Purchase recorded and stock updated.');
        } catch (\Throwable $e) {
            Session::flash('error', 'Failed to record purchase: ' . $e->getMessage());
        }

        $this->redirect('/purchases');
    }

    public function history(): void
    {
        $date = $this->input('date');
        $this->view('purchases/history', [
            'title' => 'Purchase History',
            'purchases' => $this->purchaseModel->history($date ?: null),
            'filterDate' => $date,
        ]);
    }
}
