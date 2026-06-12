<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Expense;
use App\Models\ExpenseCategory;

class ExpenseController extends Controller
{
    private Expense $expenseModel;
    private ExpenseCategory $categoryModel;

    public function __construct()
    {
        $this->expenseModel = new Expense();
        $this->categoryModel = new ExpenseCategory();
        require_once APP_PATH . '/Helpers/functions.php';
    }

    public function index(): void
    {
        $this->view('expenses/index', [
            'title' => 'Expenses',
            'expenses' => $this->expenseModel->all(),
        ]);
    }

    public function create(): void
    {
        $this->view('expenses/create', [
            'title' => 'Add Expense',
            'categories' => $this->categoryModel->all(),
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $data = $this->validateExpense();

        $this->expenseModel->create(array_merge($data, ['user_id' => Auth::id()]));
        Session::flash('success', 'Expense recorded successfully.');
        $this->redirect('/expenses');
    }

    public function edit(int $id): void
    {
        $expense = $this->expenseModel->find($id);
        if (!$expense) {
            Session::flash('error', 'Expense not found.');
            $this->redirect('/expenses');
        }

        $this->view('expenses/edit', [
            'title' => 'Edit Expense',
            'expense' => $expense,
            'categories' => $this->categoryModel->all(),
        ]);
    }

    public function update(int $id): void
    {
        $this->validateCsrf();
        $expense = $this->expenseModel->find($id);
        if (!$expense) {
            Session::flash('error', 'Expense not found.');
            $this->redirect('/expenses');
        }

        $data = $this->validateExpense();
        $this->expenseModel->update($id, $data);
        Session::flash('success', 'Expense updated successfully.');
        $this->redirect('/expenses');
    }

    public function delete(int $id): void
    {
        $this->validateCsrf();
        $this->expenseModel->delete($id);
        Session::flash('success', 'Expense deleted successfully.');
        $this->redirect('/expenses');
    }

    private function validateExpense(): array
    {
        $categoryId = (int) $this->input('category_id', 0);
        $title = trim((string) $this->input('title', ''));
        $amount = (float) $this->input('amount', 0);
        $expenseDate = (string) $this->input('expense_date', date('Y-m-d'));
        $notes = trim((string) $this->input('notes', ''));

        if ($categoryId <= 0 || $title === '' || $amount <= 0) {
            Session::flash('error', 'Please provide valid expense details.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/expenses');
        }

        return [
            'category_id' => $categoryId,
            'title' => $title,
            'amount' => $amount,
            'expense_date' => $expenseDate,
            'notes' => $notes ?: null,
        ];
    }
}
