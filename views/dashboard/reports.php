<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/ReportController.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$reportController = new ReportController($pdo);
$summary = null;

$filters = [
    'borrower' => $_POST['borrower'] ?? '',
    'status' => $_POST['status'] ?? '',
    'from' => $_POST['from'] ?? date('Y-m-01'),
    'to' => $_POST['to'] ?? date('Y-m-d')
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $summary = $reportController->generateFilteredSummary($filters);
}

$pageTitle = 'Reports';
include __DIR__ . '/../layout/header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container mt-4">
    <h2 class="text-primary mb-4">📊 Report Filters</h2>

    <form method="POST" class="row g-3 align-items-end mb-5">
        <div class="col-md-4">
            <label for="borrower" class="form-label">Borrower</label>
            <input type="text" id="borrower" name="borrower" value="<?= htmlspecialchars($filters['borrower']) ?>" class="form-control">
        </div>
        <div class="col-md-2">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All</option>
                <option value="Paid" <?= $filters['status'] === 'Paid' ? 'selected' : '' ?>>Paid</option>
                <option value="Unpaid" <?= $filters['status'] === 'Unpaid' ? 'selected' : '' ?>>Unpaid</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="from" class="form-label">From</label>
            <input type="date" id="from" name="from" value="<?= $filters['from'] ?>" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="to" class="form-label">To</label>
            <input type="date" id="to" name="to" value="<?= $filters['to'] ?>" class="form-control">
        </div>
        <div class="col-md-12 text-end">
            <button type="submit" class="btn btn-primary">🔍 Generate Report</button>
        </div>
    </form>

    <?php if ($summary): ?>
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white text-center h-100">
                    <div class="card-body">
                        <h5>Total Loans</h5>
                        <p class="fs-4"><?= $summary['loan_count'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-secondary text-white text-center h-100">
                    <div class="card-body">
                        <h5>Total Capital Lent</h5>
                        <p class="fs-4">K<?= number_format($summary['total_lent'], 2) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white text-center h-100">
                    <div class="card-body">
                        <h5>Interest Earned</h5>
                        <p class="fs-4">K<?= number_format($summary['interest_earned'], 2) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white text-center h-100">
                    <div class="card-body">
                        <h5>Unpaid</h5>
                        <p class="fs-4">K<?= number_format($summary['unpaid'], 2) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mb-5">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">📈 Monthly Interest</h5>
                        <canvas id="incomeBarChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">📊 Loan Status</h5>
                        <canvas id="loanPieChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Borrowers Table -->
        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <h5 class="card-title">🏅 Top Borrowers by Interest</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Borrower</th>
                            <th>Total Interest (K)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($summary['top_borrowers'] as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['full_name']) ?></td>
                                <td>K<?= number_format($row['total_interest'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>


        <!-- Export -->
        <div class="text-center mb-4">
            <h5>📤 Export Options</h5>
            </br>

            <form action="/loan-tracker/export/export_pdf.php" method="POST" target="_blank" class="d-inline">
                <?php foreach ($filters as $key => $val): ?>
                    <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($val) ?>">
                <?php endforeach; ?>
                <button type="submit" class="btn btn-outline-danger me-2">Export to PDF</button>
            </form>

            <form action="/loan-tracker/export/export_excel.php" method="POST" target="_blank" class="d-inline">
                <?php foreach ($filters as $key => $val): ?>
                    <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($val) ?>">
                <?php endforeach; ?>
                <button type="submit" class="btn btn-outline-success">Export to Excel</button>
            </form>
        </div>


        <!-- Chart Data Injection -->
        <script>
            const chartData = {
                months: <?= json_encode($summary['months']) ?>,
                interest: <?= json_encode($summary['interest']) ?>,
                status: <?= json_encode([$summary['paid_loans'], $summary['unpaid_loans']]) ?>
            };

            new Chart(document.getElementById('incomeBarChart'), {
                type: 'bar',
                data: {
                    labels: chartData.months,
                    datasets: [{
                        label: 'Interest Earned (K)',
                        data: chartData.interest,
                        backgroundColor: '#0d6efd'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            new Chart(document.getElementById('loanPieChart'), {
                type: 'pie',
                data: {
                    labels: ['Paid', 'Unpaid'],
                    datasets: [{
                        data: chartData.status,
                        backgroundColor: ['#198754', '#dc3545']
                    }]
                }
            });
        </script>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>