<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Item;

class ItemController extends Controller
{
    private Item $itemModel;

    public function __construct()
    {
        $this->itemModel = new Item();
        require_once APP_PATH . '/Helpers/functions.php';
    }

    public function index(): void
    {
        $this->view('items/index', [
            'title' => 'Inventory Items',
            'items' => $this->itemModel->all(),
        ]);
    }

    public function create(): void
    {
        $this->view('items/create', ['title' => 'Add Item']);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $data = $this->validateItem();

        if ($this->itemModel->nameExists($data['name'])) {
            Session::flash('error', 'An item with this name already exists.');
            set_old($data);
            $this->redirect('/items/create');
        }

        $this->itemModel->create($data);
        Session::flash('success', 'Item created successfully.');
        $this->redirect('/items');
    }

    public function edit(int $id): void
    {
        $item = $this->itemModel->find($id);
        if (!$item) {
            Session::flash('error', 'Item not found.');
            $this->redirect('/items');
        }

        $this->view('items/edit', [
            'title' => 'Edit Item',
            'item' => $item,
        ]);
    }

    public function update(int $id): void
    {
        $this->validateCsrf();
        $item = $this->itemModel->find($id);
        if (!$item) {
            Session::flash('error', 'Item not found.');
            $this->redirect('/items');
        }

        $data = $this->validateItem();
        if ($this->itemModel->nameExists($data['name'], $id)) {
            Session::flash('error', 'An item with this name already exists.');
            set_old($data);
            $this->redirect("/items/edit/{$id}");
        }

        $this->itemModel->update($id, $data);
        Session::flash('success', 'Item updated successfully.');
        $this->redirect('/items');
    }

    public function delete(int $id): void
    {
        $this->validateCsrf();
        $this->itemModel->delete($id);
        Session::flash('success', 'Item deleted successfully.');
        $this->redirect('/items');
    }

    private function validateItem(): array
    {
        $name = trim((string) $this->input('name', ''));
        $costPrice = (float) $this->input('cost_price', 0);
        $sellingPrice = (float) $this->input('selling_price', 0);
        $stock = (float) $this->input('current_stock', 0);

        if ($name === '') {
            Session::flash('error', 'Item name is required.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/items');
        }

        if ($costPrice < 0 || $sellingPrice < 0 || $stock < 0) {
            Session::flash('error', 'Prices and stock cannot be negative.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/items');
        }

        return [
            'name' => $name,
            'item_type' => in_array($this->input('item_type'), ['daily', 'long'], true) ? $this->input('item_type') : 'long',
            'cost_price' => $costPrice,
            'selling_price' => $sellingPrice,
            'current_stock' => $stock,
            'reorder_level' => max(0, (float) $this->input('reorder_level', 10)),
            'unit' => trim((string) $this->input('unit', 'pcs')) ?: 'pcs',
        ];
    }
}
