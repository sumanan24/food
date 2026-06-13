<div class="row g-3 g-md-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card report-card report-card-daily">
            <div class="card-body">
                <div class="report-card-icon"><i class="bi bi-calendar-day"></i></div>
                <h5>Daily Report</h5>
                <p class="text-muted small mb-3">View today's summary</p>
                <form method="GET" action="<?= url('reports/daily') ?>">
                    <input type="date" name="date" class="form-control mb-2" value="<?= date('Y-m-d') ?>">
                    <button type="submit" class="btn btn-primary w-100">View Report</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card report-card report-card-weekly">
            <div class="card-body">
                <div class="report-card-icon"><i class="bi bi-calendar-week"></i></div>
                <h5>Weekly Report</h5>
                <p class="text-muted small mb-3">This week's performance</p>
                <form method="GET" action="<?= url('reports/weekly') ?>">
                    <input type="date" name="date" class="form-control mb-2" value="<?= date('Y-m-d') ?>">
                    <button type="submit" class="btn btn-success w-100">View Report</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card report-card report-card-monthly">
            <div class="card-body">
                <div class="report-card-icon"><i class="bi bi-calendar-month"></i></div>
                <h5>Monthly Report</h5>
                <p class="text-muted small mb-3">Full month breakdown</p>
                <form method="GET" action="<?= url('reports/monthly') ?>">
                    <input type="month" name="date" class="form-control mb-2" value="<?= date('Y-m') ?>">
                    <button type="submit" class="btn btn-warning w-100">View Report</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card report-card report-card-yearly">
            <div class="card-body">
                <div class="report-card-icon"><i class="bi bi-calendar"></i></div>
                <h5>Yearly Report</h5>
                <p class="text-muted small mb-3">Annual overview</p>
                <form method="GET" action="<?= url('reports/yearly') ?>">
                    <input type="number" name="year" class="form-control mb-2" value="<?= date('Y') ?>" min="2000" max="2100">
                    <button type="submit" class="btn btn-primary w-100" style="background:linear-gradient(135deg,#3b82f6,#60a5fa);border:none;">View Report</button>
                </form>
            </div>
        </div>
    </div>
</div>
