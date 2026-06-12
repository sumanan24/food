<div class="page-header">
    <h1 class="h3 mb-0">Reports</h1>
</div>

<div class="row g-4">
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-calendar-day display-4 text-primary"></i>
                <h5 class="mt-3">Daily Report</h5>
                <form method="GET" action="<?= url('reports/daily') ?>" class="mt-3">
                    <input type="date" name="date" class="form-control mb-2" value="<?= date('Y-m-d') ?>">
                    <button type="submit" class="btn btn-primary w-100">View</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-calendar-week display-4 text-success"></i>
                <h5 class="mt-3">Weekly Report</h5>
                <form method="GET" action="<?= url('reports/weekly') ?>" class="mt-3">
                    <input type="date" name="date" class="form-control mb-2" value="<?= date('Y-m-d') ?>">
                    <button type="submit" class="btn btn-success w-100">View</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-calendar-month display-4 text-warning"></i>
                <h5 class="mt-3">Monthly Report</h5>
                <form method="GET" action="<?= url('reports/monthly') ?>" class="mt-3">
                    <input type="month" name="date" class="form-control mb-2" value="<?= date('Y-m') ?>">
                    <button type="submit" class="btn btn-warning w-100">View</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-calendar display-4 text-info"></i>
                <h5 class="mt-3">Yearly Report</h5>
                <form method="GET" action="<?= url('reports/yearly') ?>" class="mt-3">
                    <input type="number" name="year" class="form-control mb-2" value="<?= date('Y') ?>" min="2000" max="2100">
                    <button type="submit" class="btn btn-info w-100">View</button>
                </form>
            </div>
        </div>
    </div>
</div>
