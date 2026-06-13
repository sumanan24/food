document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('sidebarClose');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');

    function openSidebar() {
        if (!sidebar) return;
        sidebar.classList.add('open');
        overlay?.classList.add('show');
        document.body.classList.add('sidebar-open');
    }

    function closeSidebar() {
        if (!sidebar) return;
        sidebar.classList.remove('open');
        overlay?.classList.remove('show');
        document.body.classList.remove('sidebar-open');
    }

    toggleBtn?.addEventListener('click', openSidebar);
    mobileMenuBtn?.addEventListener('click', openSidebar);
    closeBtn?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    sidebar?.querySelectorAll('.sidebar-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 992) {
                closeSidebar();
            }
        });
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992) {
            closeSidebar();
        }
    });

    if (typeof $.fn.DataTable !== 'undefined') {
        $('.data-table').DataTable({
            pageLength: 25,
            order: [],
            responsive: true,
            autoWidth: false,
            language: {
                search: '',
                searchPlaceholder: 'Search records...',
                lengthMenu: 'Show _MENU_',
                info: '_START_–_END_ of _TOTAL_',
                infoEmpty: 'No records',
                paginate: {
                    previous: '<i class="bi bi-chevron-left"></i>',
                    next: '<i class="bi bi-chevron-right"></i>'
                }
            },
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rt<"row align-items-center mt-3"<"col-sm-6"i><"col-sm-6"p>>'
        });
    }

    document.querySelectorAll('.alert-app').forEach(function (alert) {
        setTimeout(function () {
            const close = alert.querySelector('.btn-close');
            if (close) close.click();
        }, 5000);
    });
});
