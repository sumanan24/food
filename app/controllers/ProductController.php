<?php
namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use Core\Controller;
use Core\Security;
use Core\Session;

class ProductController extends Controller
{
    private ProductModel $products;
    private CategoryModel $categories;

    public function __construct()
    {
        $this->products = new ProductModel();
        $this->categories = new CategoryModel();
    }

    public function index(): void
    {
        $this->requireAuth();
        $filters = [
            'search'      => $_GET['search'] ?? '',
            'category_id' => $_GET['category_id'] ?? '',
            'status'      => $_GET['status'] ?? '',
            'low_stock'   => isset($_GET['low_stock']),
        ];
        $this->view('products.index', [
            'title'      => 'Products',
            'products'   => $this->products->getAllWithCategory($filters),
            'categories' => $this->categories->active(),
            'filters'    => $filters,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->view('products.form', [
            'title'      => 'Add Product',
            'product'    => null,
            'categories' => $this->categories->active(),
        ]);
    }

    public function store(): void
    {
        $this->requireStaff();
        $this->validateCsrf();
        $data = $this->collectData();
        $errors = Security::validateRequired(['name' => 'Name', 'buying_price' => 'Buying Price', 'selling_price' => 'Selling Price'], $data);
        if ($errors) {
            Session::flash('error', implode(' ', $errors));
            redirect('products/create');
        }
        $data['image'] = $this->handleUpload();
        $id = $this->products->create($data);
        $this->logActivity('create', 'products', "Product #$id created");
        Session::flash('success', 'Product added successfully.');
        redirect('products');
    }

    public function edit(string $id): void
    {
        $this->requireAuth();
        $product = $this->products->find((int)$id);
        if (!$product) {
            Session::flash('error', 'Product not found.');
            redirect('products');
        }
        $this->view('products.form', [
            'title'      => 'Edit Product',
            'product'    => $product,
            'categories' => $this->categories->active(),
        ]);
    }

    public function update(string $id): void
    {
        $this->requireStaff();
        $this->validateCsrf();
        $data = $this->collectData();
        if (!empty($_FILES['image']['name'])) {
            $data['image'] = $this->handleUpload();
        } else {
            $existing = $this->products->find((int)$id);
            $data['image'] = $existing['image'] ?? null;
        }
        $this->products->update((int)$id, $data);
        $this->logActivity('update', 'products', "Product #$id updated");
        Session::flash('success', 'Product updated.');
        redirect('products');
    }

    public function delete(string $id): void
    {
        $this->requireStaff();
        $this->validateCsrf();
        $this->products->delete((int)$id);
        $this->logActivity('delete', 'products', "Product #$id deleted");
        Session::flash('success', 'Product deleted.');
        redirect('products');
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $product = $this->products->find((int)$id);
        if (!$product) {
            redirect('products');
        }
        $cat = $product['category_id'] ? (new CategoryModel())->find($product['category_id']) : null;
        $product['category_name'] = $cat['name'] ?? 'N/A';
        $this->view('products.view', ['title' => 'View Product', 'product' => $product]);
    }

    public function search(): void
    {
        $this->requireAuth();
        $q = $_GET['q'] ?? '';
        $products = $this->products->getAllWithCategory(['search' => $q, 'status' => '1']);
        $this->json(['success' => true, 'products' => $products]);
    }

    public function barcode(string $code): void
    {
        $this->requireAuth();
        $product = $this->products->findByBarcode($code);
        if (!$product) {
            $this->json(['success' => false, 'message' => 'Product not found'], 404);
        }
        $this->json(['success' => true, 'product' => $product]);
    }

    private function collectData(): array
    {
        return [
            'name'          => Security::sanitize($_POST['name'] ?? ''),
            'category_id'   => (int)($_POST['category_id'] ?? 0),
            'buying_price'  => (float)($_POST['buying_price'] ?? 0),
            'selling_price' => (float)($_POST['selling_price'] ?? 0),
            'quantity'      => (int)($_POST['quantity'] ?? 0),
            'barcode'       => Security::sanitize($_POST['barcode'] ?? ''),
            'expiry_date'   => $_POST['expiry_date'] ?? null,
            'status'        => (int)($_POST['status'] ?? 1),
        ];
    }

    private function handleUpload(): ?string
    {
        if (empty($_FILES['image']['name'])) return null;
        $config = require dirname(__DIR__, 2) . '/config/app.php';
        $dir = $config['upload_path'];
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) return null;
        $filename = uniqid('prod_') . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $filename)) {
            return $filename;
        }
        return null;
    }
}
