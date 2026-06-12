<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\ExpenseCategory;

class ExpenseCategoryController extends Controller
{
    private ExpenseCategory $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new ExpenseCategory();
        require_once APP_PATH . '/Helpers/functions.php';
    }

    public function index(): void
    {
        $this->view('expense-categories/index', [
            'title' => 'Expense Categories',
            'categories' => $this->categoryModel->all(),
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $name = trim((string) $this->input('name', ''));
        $description = trim((string) $this->input('description', ''));

        if ($name === '') {
            Session::flash('error', 'Category name is required.');
            $this->redirect('/expense-categories');
        }

        if ($this->categoryModel->nameExists($name)) {
            Session::flash('error', 'Category already exists.');
            $this->redirect('/expense-categories');
        }

        $this->categoryModel->create([
            'name' => $name,
            'description' => $description ?: null,
        ]);

        Session::flash('success', 'Category created successfully.');
        $this->redirect('/expense-categories');
    }

    public function update(int $id): void
    {
        $this->validateCsrf();
        $name = trim((string) $this->input('name', ''));
        $description = trim((string) $this->input('description', ''));

        if ($name === '') {
            Session::flash('error', 'Category name is required.');
            $this->redirect('/expense-categories');
        }

        if ($this->categoryModel->nameExists($name, $id)) {
            Session::flash('error', 'Category already exists.');
            $this->redirect('/expense-categories');
        }

        $this->categoryModel->update($id, [
            'name' => $name,
            'description' => $description ?: null,
        ]);

        Session::flash('success', 'Category updated successfully.');
        $this->redirect('/expense-categories');
    }

    public function delete(int $id): void
    {
        $this->validateCsrf();
        if (!$this->categoryModel->delete($id)) {
            Session::flash('error', 'Cannot delete category with existing expenses.');
            $this->redirect('/expense-categories');
        }
        Session::flash('success', 'Category deleted successfully.');
        $this->redirect('/expense-categories');
    }
}
