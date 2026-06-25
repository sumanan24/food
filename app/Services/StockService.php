<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Item;
use App\Models\StockLedger;

class StockService
{
    private Item $itemModel;
    private StockLedger $ledgerModel;

    public function __construct()
    {
        $this->itemModel = new Item();
        $this->ledgerModel = new StockLedger();
    }

    public function recordPurchase(int $itemId, int $userId, float $qty, int $purchaseId, string $date): void
    {
        $item = $this->itemModel->find($itemId);
        if (!$item || $item['item_type'] !== 'long') {
            return;
        }

        $this->itemModel->increaseStock($itemId, $qty);
        $updated = $this->itemModel->find($itemId);
        $this->ledgerModel->add([
            'item_id' => $itemId,
            'user_id' => $userId,
            'transaction_type' => 'purchase',
            'reference_id' => $purchaseId,
            'quantity_in' => $qty,
            'quantity_out' => 0,
            'balance_after' => (float) $updated['current_stock'],
            'ledger_date' => $date,
            'notes' => 'Purchase #' . $purchaseId,
        ]);
    }

    public function recordSale(int $itemId, int $userId, float $qty, ?int $referenceId, string $date, string $type = 'sale'): bool
    {
        $item = $this->itemModel->find($itemId);
        if (!$item) {
            return false;
        }

        if ($item['item_type'] === 'daily') {
            return true;
        }

        if (!$this->itemModel->decreaseStock($itemId, $qty)) {
            return false;
        }

        $updated = $this->itemModel->find($itemId);
        $this->ledgerModel->add([
            'item_id' => $itemId,
            'user_id' => $userId,
            'transaction_type' => $type,
            'reference_id' => $referenceId,
            'quantity_in' => 0,
            'quantity_out' => $qty,
            'balance_after' => (float) $updated['current_stock'],
            'ledger_date' => $date,
            'notes' => ucfirst($type) . ' transaction',
        ]);

        return true;
    }

    public function recordWastage(int $itemId, int $userId, float $qty, int $wastageId, string $date): void
    {
        $item = $this->itemModel->find($itemId);
        if (!$item) {
            return;
        }

        if ($item['item_type'] === 'long') {
            $this->itemModel->decreaseStock($itemId, $qty);
            $updated = $this->itemModel->find($itemId);
            $this->ledgerModel->add([
                'item_id' => $itemId,
                'user_id' => $userId,
                'transaction_type' => 'wastage',
                'reference_id' => $wastageId,
                'quantity_in' => 0,
                'quantity_out' => $qty,
                'balance_after' => (float) $updated['current_stock'],
                'ledger_date' => $date,
                'notes' => 'Wastage #' . $wastageId,
            ]);
        }
    }
}
