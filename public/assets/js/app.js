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
        const dtLanguage = {
            search: '',
            searchPlaceholder: 'Search records...',
            lengthMenu: 'Show _MENU_',
            info: '_START_–_END_ of _TOTAL_',
            infoEmpty: 'No records',
            paginate: {
                previous: '<i class="bi bi-chevron-left"></i>',
                next: '<i class="bi bi-chevron-right"></i>'
            }
        };

        const dtOptions = {
            pageLength: 25,
            order: [],
            responsive: true,
            autoWidth: false,
            language: dtLanguage,
            dom: '<"row align-items-center mb-3 g-2"<"col-sm-6"l><"col-sm-6"f>>rt<"row align-items-center mt-3 g-2"<"col-sm-6"i><"col-sm-6"p>>'
        };

        const dtOptionsExternal = {
            ...dtOptions,
            dom: '<"row align-items-center mb-3 g-2"<"col-sm-6"l>>rt<"row align-items-center mt-3 g-2"<"col-sm-6"i><"col-sm-6"p>>'
        };

        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            const table = settings.nTable;
            if (!table || !table._filterSelects) {
                return true;
            }

            const row = settings.aoData[dataIndex]?.nTr;
            if (!row) {
                return true;
            }

            for (const [attr, val] of Object.entries(table._filterSelects)) {
                if (row.getAttribute(attr) !== val) {
                    return false;
                }
            }

            const dateFrom = table._filterDateFrom;
            const dateTo = table._filterDateTo;
            if (dateFrom || dateTo) {
                const rowDate = row.getAttribute('data-filter-date');
                if (!rowDate) {
                    return false;
                }
                if (dateFrom && rowDate < dateFrom) {
                    return false;
                }
                if (dateTo && rowDate > dateTo) {
                    return false;
                }
            }

            return true;
        });

        $('.data-table').DataTable(dtOptions);

        if (window.innerWidth >= 992) {
            $('.data-table-lg:not([data-filter-table])').DataTable(dtOptions);
            $('[data-filter-scope] [data-filter-table]').each(function () {
                $(this).DataTable(dtOptionsExternal);
            });
        }

        initListFilterScopes();
    } else {
        initListFilterScopes();
    }

    document.querySelectorAll('.alert-app').forEach(function (alert) {
        setTimeout(function () {
            const close = alert.querySelector('.btn-close');
            if (close) close.click();
        }, 5000);
    });
});

function initListFilterScopes() {
    document.querySelectorAll('[data-filter-scope]').forEach(function (scopeEl) {
        const searchInput = scopeEl.querySelector('[data-filter-search]');
        const selects = scopeEl.querySelectorAll('[data-filter-select]');
        const dateFromInput = scopeEl.querySelector('[data-filter-date-from]');
        const dateToInput = scopeEl.querySelector('[data-filter-date-to]');
        const clearBtn = scopeEl.querySelector('[data-filter-clear]');
        const statusEl = scopeEl.querySelector('[data-filter-status]');
        const emptyEl = scopeEl.querySelector('[data-filter-empty]');
        const tableEl = scopeEl.querySelector('[data-filter-table]');
        const dt = tableEl && typeof $ !== 'undefined' && $.fn.DataTable && $.fn.DataTable.isDataTable(tableEl)
            ? $(tableEl).DataTable()
            : null;

        function getItems() {
            return scopeEl.querySelectorAll('[data-filter-item]');
        }

        function getSelectValues() {
            const vals = {};
            selects.forEach(function (sel) {
                const attr = sel.dataset.filterAttribute;
                if (attr && sel.value) {
                    vals[attr] = sel.value;
                }
            });
            return vals;
        }

        function itemMatches(item, query, selectVals, dateFrom, dateTo) {
            if (query && !item.textContent.toLowerCase().includes(query)) {
                return false;
            }

            for (const [attr, val] of Object.entries(selectVals)) {
                if (item.getAttribute(attr) !== val) {
                    return false;
                }
            }

            if (dateFrom || dateTo) {
                const rowDate = item.getAttribute('data-filter-date');
                if (!rowDate) {
                    return false;
                }
                if (dateFrom && rowDate < dateFrom) {
                    return false;
                }
                if (dateTo && rowDate > dateTo) {
                    return false;
                }
            }

            return true;
        }

        function applyFilters() {
            const query = (searchInput?.value || '').trim().toLowerCase();
            const selectVals = getSelectValues();
            const dateFrom = dateFromInput?.value || '';
            const dateTo = dateToInput?.value || '';
            const items = getItems();
            let visible = 0;

            items.forEach(function (item) {
                const show = itemMatches(item, query, selectVals, dateFrom, dateTo);
                item.classList.toggle('d-none', !show);
                if (show) {
                    visible++;
                }
            });

            if (tableEl && dt) {
                tableEl._filterSelects = selectVals;
                tableEl._filterDateFrom = dateFrom;
                tableEl._filterDateTo = dateTo;
                dt.search(query).draw();
            }

            const hasFilter = query || Object.keys(selectVals).length > 0 || dateFrom || dateTo;
            if (statusEl) {
                statusEl.classList.toggle('d-none', !hasFilter || items.length === 0);
                if (hasFilter && items.length > 0) {
                    statusEl.textContent = 'Showing ' + visible + ' of ' + items.length + ' record' + (items.length !== 1 ? 's' : '');
                }
            }

            if (emptyEl) {
                emptyEl.classList.toggle('d-none', visible > 0 || items.length === 0);
            }
        }

        searchInput?.addEventListener('input', applyFilters);
        dateFromInput?.addEventListener('change', applyFilters);
        dateToInput?.addEventListener('change', applyFilters);
        selects.forEach(function (sel) {
            sel.addEventListener('change', applyFilters);
        });
        clearBtn?.addEventListener('click', function () {
            if (searchInput) {
                searchInput.value = '';
            }
            if (dateFromInput) {
                dateFromInput.value = '';
            }
            if (dateToInput) {
                dateToInput.value = '';
            }
            selects.forEach(function (sel) {
                sel.selectedIndex = 0;
            });
            applyFilters();
        });
    });
}
