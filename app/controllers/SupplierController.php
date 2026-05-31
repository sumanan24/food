<?php
namespace App\Controllers;

use App\Models\SupplierModel;
use Core\Controller;
use Core\Security;
use Core\Session;

class SupplierController extends Controller
{
    private SupplierModel $suppliers;

    public function __construct()
    {
        $this->suppliers = new SupplierModel();
    }

    public function index(): void
    {
        $this->requireAuth();
        $this->view('suppliers.index', [
            'title'     => 'Suppliers',
            'suppliers' => $this->suppliers->all('name ASC'),
        ]);
    }

    public function store(): void
    {
        $this->requireStaff();
        $this->validateCsrf();
        $this->suppliers->create($this->data());
        Session::flash('success', 'Supplier added.');
        redirect('suppliers');
    }

    public function update(string $id): void
    {
        $this->requireStaff();
        $this->validateCsrf();
        $this->suppliers->update((int)$id, $this->data());
        Session::flash('success', 'Supplier updated.');
        redirect('suppliers');
    }

    public function delete(string $id): void
    {
        $this->requireStaff();
        $this->validateCsrf();
        $this->suppliers->delete((int)$id);
        Session::flash('success', 'Supplier deleted.');
        redirect('suppliers');
    }

    private function data(): array
    {
        return [
            'name'           => Security::sanitize($_POST['name'] ?? ''),
            'contact_number' => Security::sanitize($_POST['contact_number'] ?? ''),
            'address'        => Security::sanitize($_POST['address'] ?? ''),
            'email'          => Security::sanitize($_POST['email'] ?? ''),
            'status'         => (int)($_POST['status'] ?? 1),
        ];
    }
}
