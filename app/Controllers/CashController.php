<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Bill;
use App\Models\CashSession;
use App\Models\Expense;

class CashController extends Controller
{
    private CashSession $cashModel;

    public function __construct()
    {
        $this->cashModel = new CashSession();
        require_once APP_PATH . '/Helpers/functions.php';
    }

    public function index(): void
    {
        $openSession = $this->cashModel->getOpenSession();
        $todaySessions = $this->cashModel->getTodaySessions();
        $billModel = new Bill();
        $expenseModel = new Expense();

        $todaySales = $billModel->todayTotal();
        $todayExpenses = $expenseModel->todayTotal();

        $sessionSales = 0.0;
        $sessionExpenses = 0.0;
        $expectedCash = 0.0;
        if ($openSession) {
            $sessionSales = $billModel->totalForSession((int) $openSession['id']);
            $sessionExpenses = $expenseModel->totalDuringSession($openSession['opened_at']);
            $expectedCash = (float) $openSession['opening_balance'] + $sessionSales - $sessionExpenses;
        }

        $this->view('cash/index', [
            'title' => 'Bill Counter',
            'session' => $openSession,
            'todaySessions' => $todaySessions,
            'todaySales' => $todaySales,
            'todayExpenses' => $todayExpenses,
            'sessionSales' => $sessionSales,
            'sessionExpenses' => $sessionExpenses,
            'expectedCash' => $expectedCash,
            'history' => $this->cashModel->history(15),
            'defaultPersonName' => Auth::user()['name'] ?? '',
        ]);
    }

    public function open(): void
    {
        $this->validateCsrf();
        $handCash = parse_amount($this->input('opening_balance', 0));
        $counterPerson = trim((string) $this->input('counter_person_name', ''));
        if ($counterPerson === '') {
            $counterPerson = Auth::user()['name'] ?? 'Counter';
        }

        if ($handCash < 0) {
            Session::flash('error', 'Open cash amount cannot be negative.');
            $this->redirect('/cash');
        }

        try {
            $this->cashModel->open(Auth::id(), $handCash, $counterPerson);
            Session::flash('success', 'Cash opened with ' . money($handCash) . '. You can now bill on POS.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }

        $this->redirect('/cash');
    }

    public function close(): void
    {
        $this->validateCsrf();
        $session = $this->cashModel->getOpenSession();

        if (!$session) {
            Session::flash('error', 'No open cash session. Open the counter first.');
            $this->redirect('/cash');
        }

        $cashReceived = parse_amount($this->input('cash_received', 0));
        $closedByName = trim((string) $this->input('closed_by_name', ''));
        if ($closedByName === '') {
            $closedByName = Auth::user()['name'] ?? 'Counter';
        }
        $notes = trim((string) $this->input('notes', ''));

        if ($cashReceived < 0) {
            Session::flash('error', 'Close cash amount cannot be negative.');
            $this->redirect('/cash');
        }

        if ($this->cashModel->close((int) $session['id'], $cashReceived, $closedByName, $notes ?: null)) {
            Session::flash('success', 'Cash closed. Counted: ' . money($cashReceived) . '. You can open again when ready.');
        } else {
            Session::flash('error', 'Failed to close cash session.');
        }

        $this->redirect('/cash');
    }
}
