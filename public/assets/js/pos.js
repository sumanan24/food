/**
 * POS Billing System — paid amount, change, balance due
 */
let cart = [];
let lastGrandTotal = 0;

window.CURRENCY = window.CURRENCY || 'Rs.';

function formatMoney(amount) {
    return window.CURRENCY + parseFloat(amount || 0).toFixed(2);
}

function calcTotals() {
    let subtotal = 0;
    cart.forEach(item => {
        subtotal += item.price * item.quantity;
    });
    const discount = parseFloat(document.getElementById('discount')?.value || 0) || 0;
    const taxRate = parseFloat(document.getElementById('taxRate')?.value || 0) || 0;
    const taxable = Math.max(0, subtotal - discount);
    const tax = Math.round(taxable * (taxRate / 100) * 100) / 100;
    const grand = Math.round((taxable + tax) * 100) / 100;
    return { subtotal, discount, tax, grand };
}

function updatePaymentSummary() {
    const { subtotal, discount, tax, grand } = calcTotals();
    lastGrandTotal = grand;

    document.getElementById('subtotalDisplay').textContent = formatMoney(subtotal);
    document.getElementById('discountDisplay').textContent = '-' + formatMoney(discount);
    document.getElementById('taxDisplay').textContent = formatMoney(tax);
    document.getElementById('grandTotal').textContent = formatMoney(grand);
    document.getElementById('grandTotalValue').value = grand;

    const paidInput = document.getElementById('paidAmount');
    const paid = parseFloat(paidInput?.value || 0) || 0;
    const change = Math.round(Math.max(0, paid - grand) * 100) / 100;
    const balance = Math.round(Math.max(0, grand - paid) * 100) / 100;

    const changeRow = document.getElementById('changeRow');
    const balanceRow = document.getElementById('balanceRow');

    if (paid > 0 && change > 0) {
        changeRow.classList.remove('d-none');
        document.getElementById('changeAmount').textContent = formatMoney(change);
    } else {
        changeRow.classList.add('d-none');
    }

    if (paid > 0 && balance > 0) {
        balanceRow.classList.remove('d-none');
        document.getElementById('balanceDue').textContent = formatMoney(balance);
    } else {
        balanceRow.classList.add('d-none');
    }

    return { subtotal, discount, tax, grand, paid, change, balance };
}

function addToCart(id, name, price, stock) {
    const existing = cart.find(i => i.product_id === id);
    if (existing) {
        if (existing.quantity >= stock) {
            showToast('Insufficient stock', 'error');
            return;
        }
        existing.quantity++;
    } else {
        cart.push({ product_id: id, product_name: name, price: parseFloat(price), quantity: 1, stock });
    }
    renderCart();
}

function removeFromCart(id) {
    cart = cart.filter(i => i.product_id !== id);
    renderCart();
}

function updateQty(id, qty) {
    const item = cart.find(i => i.product_id === id);
    if (item) {
        item.quantity = Math.max(1, Math.min(parseInt(qty, 10) || 1, item.stock));
        renderCart();
    }
}

function renderCart() {
    const tbody = document.querySelector('#cartTable tbody');
    tbody.innerHTML = '';
    cart.forEach(item => {
        const line = item.price * item.quantity;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${item.product_name}</td>
            <td><input type="number" min="1" max="${item.stock}" value="${item.quantity}" class="form-control form-control-sm" style="width:60px"></td>
            <td>${line.toFixed(2)}</td>
            <td><button type="button" class="btn btn-sm btn-danger">&times;</button></td>`;
        tr.querySelector('input').addEventListener('change', function () {
            updateQty(item.product_id, this.value);
        });
        tr.querySelector('button').addEventListener('click', () => removeFromCart(item.product_id));
        tbody.appendChild(tr);
    });

    const { grand } = calcTotals();
    const paidInput = document.getElementById('paidAmount');
    const currentPaid = parseFloat(paidInput.value || 0) || 0;

    // Auto-fill paid = grand when cart changes (if empty or was matching previous total)
    if (cart.length === 0) {
        paidInput.value = '0.00';
    } else if (currentPaid === 0 || Math.abs(currentPaid - lastGrandTotal) < 0.01) {
        paidInput.value = grand.toFixed(2);
    }

    document.getElementById('cartData').value = JSON.stringify(cart);
    updatePaymentSummary();
}

function setExactPaid() {
    const grand = parseFloat(document.getElementById('grandTotalValue').value || 0);
    document.getElementById('paidAmount').value = grand.toFixed(2);
    updatePaymentSummary();
}

document.querySelectorAll('.product-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        addToCart(
            parseInt(btn.dataset.id, 10),
            btn.dataset.name,
            btn.dataset.price,
            parseInt(btn.dataset.stock, 10)
        );
    });
});

document.getElementById('discount')?.addEventListener('input', renderCart);

document.getElementById('paidAmount')?.addEventListener('input', updatePaymentSummary);
document.getElementById('paidAmount')?.addEventListener('change', updatePaymentSummary);

document.getElementById('paymentType')?.addEventListener('change', function () {
    const type = this.value;
    if (type === 'card' || type === 'upi' || type === 'bank') {
        setExactPaid();
    }
});

// Quick amount buttons — insert after paid amount on DOM ready
document.addEventListener('DOMContentLoaded', function () {
    const paidWrap = document.getElementById('paidAmount')?.parentElement;
    if (paidWrap) {
        const quick = document.createElement('div');
        quick.className = 'd-flex gap-1 mt-1 flex-wrap';
        quick.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnExact">Exact</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-add="100">+100</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-add="500">+500</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-add="1000">+1000</button>`;
        paidWrap.appendChild(quick);
        document.getElementById('btnExact')?.addEventListener('click', setExactPaid);
        quick.querySelectorAll('[data-add]').forEach(btn => {
            btn.addEventListener('click', function () {
                const add = parseFloat(this.dataset.add);
                const grand = parseFloat(document.getElementById('grandTotalValue').value || 0);
                const paid = parseFloat(document.getElementById('paidAmount').value || 0) || grand;
                document.getElementById('paidAmount').value = (paid + add).toFixed(2);
                updatePaymentSummary();
            });
        });
    }
    window.CURRENCY = document.getElementById('grandTotal')?.textContent?.replace(/[\d.,]/g, '').trim() || 'Rs.';
});

// Barcode scan
document.getElementById('barcodeInput')?.addEventListener('keypress', function (e) {
    if (e.key !== 'Enter') return;
    e.preventDefault();
    const code = this.value.trim();
    if (!code) return;
    (window.apiFetch || fetch)((window.APP_URL || '') + '/api/products/barcode/' + encodeURIComponent(code))
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                addToCart(res.product.id, res.product.name, res.product.selling_price, res.product.quantity);
                this.value = '';
            } else showToast(res.message || 'Not found', 'error');
        });
});

// Checkout AJAX
$('#checkoutForm').on('submit', function (e) {
    e.preventDefault();
    if (cart.length === 0) {
        showToast('Cart is empty', 'error');
        return;
    }

    const { grand, paid, balance } = updatePaymentSummary();

    if (paid < grand) {
        showToast('Paid amount must be equal or greater than grand total. Balance due: ' + formatMoney(balance), 'error');
        document.getElementById('paidAmount').focus();
        return;
    }

    const formData = new FormData(this);
    formData.append('customer_name', document.getElementById('customerName').value);
    formData.append('discount', document.getElementById('discount').value);
    formData.append('payment_type', document.getElementById('paymentType').value);
    formData.append('paid_amount', paid.toFixed(2));
    formData.append('cart', JSON.stringify(cart));

    $.ajax({
        url: (window.APP_URL || '') + '/sales/checkout',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.CSRF_TOKEN || '',
        },
        success(res) {
            if (res.success) {
                showToast('Sale completed! Change: ' + formatMoney(Math.max(0, paid - grand)));
                window.open(res.redirect, '_blank');
                cart = [];
                document.getElementById('customerName').value = '';
                document.getElementById('discount').value = '0';
                renderCart();
            } else showToast(res.message, 'error');
        },
        error(xhr) {
            const res = xhr.responseJSON;
            showToast(res?.message || 'Checkout failed', 'error');
        }
    });
});
