<?php
namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\PurchaseModel;
use App\Models\SupplierModel;
use Core\Controller;
use Core\Security;
use Core\Session;

class PurchaseController extends Controller
{
    private PurchaseModel $purchases;

    public function __construct()
    {
        $this->purchases = new PurchaseModel();
    }

    public function index(): void
    {
        $this->requireAuth();
        $filters = ['from' => $_GET['from'] ?? '', 'to' => $_GET['to'] ?? ''];
        $this->view('purchases.index', [
            'title'     => 'Purchases',
            'purchases' => $this->purchases->getAll($filters),
            'filters'   => $filters,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->view('purchases.form', [
            'title'     => 'Add Purchase',
            'purchase'  => null,
            'products'  => (new ProductModel())->getAllWithCategory(['status' => '1']),
            'suppliers' => (new SupplierModel())->active(),
        ]);
    }

    public function store(): void
    {
        $this->requireStaff();
        $this->validateCsrf();
        $data = $this->collectData();
        $this->purchases->create($data);
        $this->logActivity('create', 'purchases');
        Session::flash('success', 'Purchase recorded.');
        redirect('purchases');
    }

    public function edit(string $id): void
    {
        $this->requireAuth();
        $purchase = $this->purchases->find((int)$id);
        if (!$purchase) redirect('purchases');
        $this->view('purchases.form', [
            'title'     => 'Edit Purchase',
            'purchase'  => $purchase,
            'products'  => (new ProductModel())->getAllWithCategory(),
            'suppliers' => (new SupplierModel())->active(),
        ]);
    }

    public function update(string $id): void
    {
        $this->requireStaff();
        $this->validateCsrf();
        $this->purchases->update((int)$id, $this->collectData());
        Session::flash('success', 'Purchase updated.');
        redirect('purchases');
    }

    public function delete(string $id): void
    {
        $this->requireStaff();
        $this->validateCsrf();
        $this->purchases->deletePurchase((int)$id);
        Session::flash('success', 'Purchase deleted.');
        redirect('purchases');
    }

    private function collectData(): array
    {
        return [
            'supplier_id'    => (int)($_POST['supplier_id'] ?? 0),
            'supplier_name'  => Security::sanitize($_POST['supplier_name'] ?? ''),
            'product_id'     => (int)($_POST['product_id'] ?? 0),
            'quantity'       => (int)($_POST['quantity'] ?? 0),
            'buying_cost'    => (float)($_POST['buying_cost'] ?? 0),
            'purchase_date'  => $_POST['purchase_date'] ?? date('Y-m-d'),
            'invoice_number' => Security::sanitize($_POST['invoice_number'] ?? ''),
            'notes'          => Security::sanitize($_POST['notes'] ?? ''),
            'user_id'        => currentUser()['id'] ?? null,
        ];
    }
}
