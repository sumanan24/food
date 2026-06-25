<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Item;
use App\Models\Wastage;

class WastageController extends Controller
{
    private Wastage $wastageModel;
    private Item $itemModel;

    public function __construct()
    {
        $this->wastageModel = new Wastage();
        $this->itemModel = new Item();
        require_once APP_PATH . '/Helpers/functions.php';
    }

    public function index(): void
    {
        $date = $this->input('date');
        $this->view('wastage/index', [
            'title' => 'Wastage',
            'records' => $this->wastageModel->history($date ?: null),
            'items' => $this->itemModel->allByType('daily'),
            'filterDate' => $date,
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();

        $itemId = (int) $this->input('item_id', 0);
        $quantity = (float) $this->input('quantity', 0);
        $date = (string) $this->input('wastage_date', date('Y-m-d'));
        $notes = trim((string) $this->input('notes', ''));

        if ($itemId <= 0 || $quantity <= 0) {
            Session::flash('error', 'Please provide valid wastage details.');
            $this->redirect('/wastage');
        }

        $item = $this->itemModel->find($itemId);
        if (!$item || $item['item_type'] !== 'daily') {
            Session::flash('error', 'Wastage can only be recorded for daily use items.');
            $this->redirect('/wastage');
        }

        try {
            $this->wastageModel->create([
                'item_id' => $itemId,
                'user_id' => Auth::id(),
                'quantity' => $quantity,
                'wastage_date' => $date,
                'notes' => $notes ?: null,
            ]);
            Session::flash('success', 'Wastage recorded successfully.');
        } catch (\Throwable $e) {
            Session::flash('error', 'Failed to record wastage: ' . $e->getMessage());
        }

        $this->redirect('/wastage');
    }
}
