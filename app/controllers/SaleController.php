<?php
namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\SaleModel;
use App\Models\SettingModel;
use Core\Controller;
use Core\Security;
use Core\Session;

class SaleController extends Controller
{
    private SaleModel $sales;
    private ProductModel $products;

    public function __construct()
    {
        $this->sales = new SaleModel();
        $this->products = new ProductModel();
    }

    public function pos(): void
    {
        $this->requireAuth();
        $settings = (new SettingModel())->getAll();
        $this->view('sales.pos', [
            'title'    => 'POS Billing',
            'products' => $this->products->getAllWithCategory(['status' => '1']),
            'settings' => $settings,
        ]);
    }

    public function checkout(): void
    {
        $this->requireAuth();
        $config = require dirname(__DIR__, 2) . '/config/app.php';
        $token = $_POST[$config['csrf_token_name']] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!\Core\Security::validateCsrf($token)) {
            $this->json(['success' => false, 'message' => 'Invalid security token'], 403);
        }
        $items = json_decode($_POST['cart'] ?? '[]', true);
        if (empty($items)) {
            $this->json(['success' => false, 'message' => 'Cart is empty'], 400);
        }

        $subtotal = 0;
        $costTotal = 0;
        $lineItems = [];

        foreach ($items as $item) {
            $product = $this->products->find((int)$item['product_id']);
            if (!$product) continue;
            $qty = max(1, (int)$item['quantity']);
            $price = (float)($item['price'] ?? $product['selling_price']);
            $lineTotal = $price * $qty;
            $subtotal += $lineTotal;
            $costTotal += $product['buying_price'] * $qty;
            $lineItems[] = [
                'product_id'   => $product['id'],
                'product_name' => $product['name'],
                'quantity'     => $qty,
                'unit_price'   => $price,
                'buying_price' => $product['buying_price'],
                'line_total'   => $lineTotal,
            ];
        }

        $discount = (float)($_POST['discount'] ?? 0);
        $taxRate = (float)((new SettingModel())->get('tax_rate', '0'));
        $taxable = max(0, $subtotal - $discount);
        $tax = round($taxable * ($taxRate / 100), 2);
        $grandTotal = $taxable + $tax;
        $paid = (float)($_POST['paid_amount'] ?? $grandTotal);

        if ($paid < $grandTotal) {
            $this->json([
                'success' => false,
                'message' => 'Paid amount (' . number_format($paid, 2) . ') is less than grand total (' . number_format($grandTotal, 2) . ').',
            ], 400);
        }

        try {
            $saleId = $this->sales->createSale([
                'invoice_number' => $this->sales->generateInvoiceNumber(),
                'sale_date'      => date('Y-m-d'),
                'customer_name'  => Security::sanitize($_POST['customer_name'] ?? ''),
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'tax'            => $tax,
                'grand_total'    => $grandTotal,
                'payment_type'   => $_POST['payment_type'] ?? 'cash',
                'paid_amount'    => $paid,
                'change_amount'  => max(0, $paid - $grandTotal),
                'cost_total'     => $costTotal,
                'notes'          => Security::sanitize($_POST['notes'] ?? ''),
                'user_id'        => currentUser()['id'] ?? null,
            ], $lineItems);

            $this->logActivity('sale', 'sales', "Invoice created #$saleId");
            $this->json(['success' => true, 'sale_id' => $saleId, 'redirect' => url('sales/print/' . $saleId)]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function history(): void
    {
        $this->requireAuth();
        $filters = ['from' => $_GET['from'] ?? '', 'to' => $_GET['to'] ?? '', 'search' => $_GET['search'] ?? ''];
        $this->view('sales.history', [
            'title' => 'Sales History',
            'sales' => $this->sales->history($filters),
            'filters' => $filters,
        ]);
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $sale = $this->sales->getWithItems((int)$id);
        if (!$sale) redirect('sales/history');
        $this->view('sales.view', [
            'title'    => 'Sale Details',
            'sale'     => $sale,
            'settings' => (new SettingModel())->getAll(),
        ]);
    }

    public function invoice(string $id): void
    {
        $this->requireAuth();
        $sale = $this->sales->getWithItems((int)$id);
        $settings = (new SettingModel())->getAll();
        $this->view('sales.invoice', ['sale' => $sale, 'settings' => $settings], 'print');
    }

    public function printBill(string $id): void
    {
        $this->invoice($id);
    }
}
