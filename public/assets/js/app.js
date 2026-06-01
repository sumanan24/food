/**
 * Food Shop Management - Main JS
 */

// Required by some hosts (openresty / ModSecurity) for /api/* requests
if (typeof jQuery !== 'undefined') {
    jQuery.ajaxSetup({
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        beforeSend: function (xhr) {
            if (window.CSRF_TOKEN) {
                xhr.setRequestHeader('X-CSRF-TOKEN', window.CSRF_TOKEN);
            }
        },
    });
}

/** fetch() wrapper with headers accepted by cPanel / openresty proxies */
window.apiFetch = function (url, options) {
    options = options || {};
    const headers = Object.assign(
        {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        options.headers || {}
    );
    if (window.CSRF_TOKEN && !headers['X-CSRF-TOKEN']) {
        headers['X-CSRF-TOKEN'] = window.CSRF_TOKEN;
    }
    return fetch(url, Object.assign({}, options, { headers }));
};

function confirmDelete(form) {
    if (!confirm('Are you sure you want to delete this item?')) return false;
    return true;
}

function showToast(message, type = 'success') {
    const el = document.createElement('div');
    el.className = `alert alert-${type === 'error' ? 'danger' : type} position-fixed top-0 end-0 m-3`;
    el.style.zIndex = 9999;
    el.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 4000);
}

// Theme toggle
document.getElementById('themeToggle')?.addEventListener('click', function () {
    const html = document.documentElement;
    const theme = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-bs-theme', theme);
    localStorage.setItem('theme', theme);
});

const savedTheme = localStorage.getItem('theme');
if (savedTheme) document.documentElement.setAttribute('data-bs-theme', savedTheme);

// Product AJAX search
let searchTimeout;
$('#productSearch')?.on('input', function () {
    clearTimeout(searchTimeout);
    const q = $(this).val();
    searchTimeout = setTimeout(() => {
        if (q.length < 2) return;
        $.get((window.APP_URL || '') + '/api/products/search', { q }, function (res) {
            if (!res.success) return;
            console.log('Found', res.products.length, 'products');
        });
    }, 300);
});
