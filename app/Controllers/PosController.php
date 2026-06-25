<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Bill;
use App\Models\CashSession;
use App\Models\Item;

class PosController extends Controller
{
    private Bill $billModel;
    private Item $itemModel;
    private CashSession $cashModel;

    public function __construct()
    {
        $this->billModel = new Bill();
        $this->itemModel = new Item();
        $this->cashModel = new CashSession();
        require_once APP_PATH . '/Helpers/functions.php';
    }

    public function index(): void
    {
        $session = $this->cashModel->getOpenSession();
        $counterOpen = $this->cashModel->isCounterOpen();

        $this->view('pos/index', [
            'title' => 'POS Billing',
            'items' => $this->itemModel->all(),
            'counterOpen' => $counterOpen,
            'session' => $session,
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();

        if (!$this->cashModel->isCounterOpen()) {
            Session::flash('error', 'Bill counter is not open. Open the counter with hand cash before billing.');
            $this->redirect('/cash');
        }

        $openSession = $this->cashModel->getOpenSession();

        $itemsJson = (string) $this->input('items', '[]');
        $items = json_decode($itemsJson, true);
        if (!is_array($items) || count($items) === 0) {
            Session::flash('error', 'Add at least one item to the bill.');
            $this->redirect('/pos');
        }

        $discount = max(0, (float) $this->input('discount', 0));
        $paymentMethod = (string) $this->input('payment_method', 'cash');
        $billDate = date('Y-m-d');
        $notes = trim((string) $this->input('notes', ''));

        $lines = [];
        $subtotal = 0;
        foreach ($items as $line) {
            $itemId = (int) ($line['item_id'] ?? 0);
            $qty = (float) ($line['quantity'] ?? 0);
            $price = (float) ($line['unit_price'] ?? 0);
            if ($itemId <= 0 || $qty <= 0) {
                continue;
            }
            $item = $this->itemModel->find($itemId);
            if (!$item) {
                continue;
            }
            if ($item['item_type'] === 'long' && (float) $item['current_stock'] < $qty) {
                Session::flash('error', 'Insufficient stock for ' . $item['name']);
                $this->redirect('/pos');
            }
            $lineTotal = $qty * $price;
            $subtotal += $lineTotal;
            $lines[] = [
                'item_id' => $itemId,
                'quantity' => $qty,
                'unit_price' => $price,
                'total_price' => $lineTotal,
            ];
        }

        if (count($lines) === 0) {
            Session::flash('error', 'Invalid bill items.');
            $this->redirect('/pos');
        }

        $total = max(0, $subtotal - $discount);
        $billNumber = $this->billModel->generateBillNumber($billDate);

        try {
            $billId = $this->billModel->createWithItems([
                'bill_number' => $billNumber,
                'user_id' => Auth::id(),
                'cash_session_id' => $openSession ? (int) $openSession['id'] : null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total_amount' => $total,
                'payment_method' => in_array($paymentMethod, ['cash', 'card', 'upi', 'other'], true) ? $paymentMethod : 'cash',
                'bill_date' => $billDate,
                'notes' => $notes ?: null,
            ], $lines);

            Session::flash('success', 'Bill ' . $billNumber . ' saved successfully.');
            $this->redirect('/pos/receipt/' . $billId);
        } catch (\Throwable $e) {
            Session::flash('error', 'Failed to save bill: ' . $e->getMessage());
            $this->redirect('/pos');
        }
    }

    public function receipt(int $id): void
    {
        $bill = $this->billModel->find($id);
        if (!$bill) {
            Session::flash('error', 'Bill not found.');
            $this->redirect('/pos');
        }

        $this->view('pos/receipt', [
            'title' => 'Receipt ' . $bill['bill_number'],
            'bill' => $bill,
            'items' => $this->billModel->getItems($id),
            'session' => !empty($bill['cash_session_id'])
                ? $this->cashModel->find((int) $bill['cash_session_id'])
                : $this->cashModel->findByDate($bill['bill_date']),
        ]);
    }

    public function history(): void
    {
        $date = $this->input('date');
        $this->view('pos/history', [
            'title' => 'Bill History',
            'bills' => $this->billModel->history($date ?: null),
            'filterDate' => $date,
        ]);
    }
}
