<?php
namespace App\Controllers;

use App\Models\CategoryModel;
use Core\Controller;
use Core\Security;
use Core\Session;

class CategoryController extends Controller
{
    private CategoryModel $categories;

    public function __construct()
    {
        $this->categories = new CategoryModel();
    }

    public function index(): void
    {
        $this->requireAuth();
        $search = $_GET['search'] ?? '';
        $list = $search ? $this->categories->search($search) : $this->categories->all('name ASC');
        $this->view('categories.index', ['title' => 'Categories', 'categories' => $list, 'search' => $search]);
    }

    public function store(): void
    {
        $this->requireStaff();
        $this->validateCsrf();
        $data = [
            'name'        => Security::sanitize($_POST['name'] ?? ''),
            'description' => Security::sanitize($_POST['description'] ?? ''),
            'status'      => (int)($_POST['status'] ?? 1),
        ];
        $this->categories->create($data);
        $this->logActivity('create', 'categories');
        Session::flash('success', 'Category added.');
        redirect('categories');
    }

    public function update(string $id): void
    {
        $this->requireStaff();
        $this->validateCsrf();
        $data = [
            'name'        => Security::sanitize($_POST['name'] ?? ''),
            'description' => Security::sanitize($_POST['description'] ?? ''),
            'status'      => (int)($_POST['status'] ?? 1),
        ];
        $this->categories->update((int)$id, $data);
        Session::flash('success', 'Category updated.');
        redirect('categories');
    }

    public function delete(string $id): void
    {
        $this->requireStaff();
        $this->validateCsrf();
        $this->categories->delete((int)$id);
        Session::flash('success', 'Category deleted.');
        redirect('categories');
    }
}
