<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\DailyBalance;
use App\Models\Item;

class DailyBalanceController extends Controller
{
    private DailyBalance $balanceModel;
    private Item $itemModel;

    public function __construct()
    {
        $this->balanceModel = new DailyBalance();
        $this->itemModel = new Item();
        require_once APP_PATH . '/Helpers/functions.php';
    }

    public function index(): void
    {
        $date = (string) ($this->input('date') ?: date('Y-m-d'));
        $this->view('daily-balance/index', [
            'title' => 'Daily Food Balance',
            'date' => $date,
            'report' => $this->balanceModel->getReport($date),
            'dailyItems' => $this->itemModel->allByType('daily'),
        ]);
    }

    public function saveOpening(): void
    {
        $this->validateCsrf();
        $date = (string) $this->input('balance_date', date('Y-m-d'));
        $openings = $this->input('opening', []);

        if (!is_array($openings)) {
            Session::flash('error', 'Invalid opening data.');
            $this->redirect('/daily-balance?date=' . urlencode($date));
        }

        foreach ($openings as $itemId => $qty) {
            $itemId = (int) $itemId;
            $qty = max(0, (float) $qty);
            if ($itemId > 0) {
                $this->balanceModel->setOpening($itemId, Auth::id(), $qty, $date);
            }
        }

        Session::flash('success', 'Opening quantities saved for ' . $date);
        $this->redirect('/daily-balance?date=' . urlencode($date));
    }
}
