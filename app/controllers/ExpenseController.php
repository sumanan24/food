<?php
namespace App\Controllers;

use App\Models\ExpenseModel;
use Core\Controller;
use Core\Security;
use Core\Session;

class ExpenseController extends Controller
{
    private ExpenseModel $expenses;

    public function __construct()
    {
        $this->expenses = new ExpenseModel();
    }

    public function index(): void
    {
        $this->requireAuth();
        $filters = [
            'from'   => $_GET['from'] ?? '',
            'to'     => $_GET['to'] ?? '',
            'search' => $_GET['search'] ?? '',
        ];
        $this->view('expenses.index', [
            'title'    => 'Expenses',
            'expenses' => $this->expenses->getAll($filters),
            'filters'  => $filters,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->view('expenses.form', [
            'title'      => 'Add Expense',
            'expense'    => null,
            'categories' => $this->expenses->categories(),
        ]);
    }

    public function store(): void
    {
        $this->requireStaff();
        $this->validateCsrf();
        $this->expenses->create($this->collect());
        Session::flash('success', 'Expense added.');
        redirect('expenses');
    }

    public function edit(string $id): void
    {
        $this->requireAuth();
        $expense = $this->expenses->find((int)$id);
        if (!$expense) redirect('expenses');
        $this->view('expenses.form', [
            'title'      => 'Edit Expense',
            'expense'    => $expense,
            'categories' => $this->expenses->categories(),
        ]);
    }

    public function update(string $id): void
    {
        $this->requireStaff();
        $this->validateCsrf();
        $this->expenses->update((int)$id, $this->collect());
        Session::flash('success', 'Expense updated.');
        redirect('expenses');
    }

    public function delete(string $id): void
    {
        $this->requireStaff();
        $this->validateCsrf();
        $this->expenses->delete((int)$id);
        Session::flash('success', 'Expense deleted.');
        redirect('expenses');
    }

    private function collect(): array
    {
        $catId = (int)($_POST['category_id'] ?? 0);
        $catName = Security::sanitize($_POST['category_name'] ?? '');
        foreach ($this->expenses->categories() as $c) {
            if ($c['id'] == $catId) {
                $catName = $c['name'];
                break;
            }
        }
        return [
            'title'         => Security::sanitize($_POST['title'] ?? ''),
            'category_id'   => $catId,
            'category_name' => $catName,
            'amount'        => (float)($_POST['amount'] ?? 0),
            'expense_date'  => $_POST['expense_date'] ?? date('Y-m-d'),
            'notes'         => Security::sanitize($_POST['notes'] ?? ''),
            'user_id'       => currentUser()['id'] ?? null,
        ];
    }
}
