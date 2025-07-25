<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/LoanController.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$loanController = new LoanController($pdo);
$loans = $loanController->getAllLoans();

// Summary stats
$totalLoans = count($loans);
$totalUnpaid = count(array_filter($loans, fn($l) => $l['status'] === 'Unpaid'));
$totalInterest = array_sum(array_map(fn($l) => $l['total_payable'] - $l['loan_amount'], $loans));

// Recent loans (last 5)
$recentLoans = array_slice(array_reverse($loans), 0, 5);

$pageTitle = 'Dashboard';
include __DIR__ . '/../views/layout/header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container mt-4">
    <h2 class="text-primary mb-4">👋 Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">📦 Total Loans</h5>
                    <p class="card-text fs-3"><?= $totalLoans ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">🕓 Unpaid Loans</h5>
                    <p class="card-text fs-3"><?= $totalUnpaid ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">💰 Total Interest</h5>
                    <p class="card-text fs-3">K<?= number_format($totalInterest, 2) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">➕ Add New Loan</h5>
                    <p class="card-text flex-grow-1">Record a new loan with borrower details and interest. The system calculates everything else.</p>
                    <a href="/loan-tracker/views/dashboard/add_loan.php" class="btn btn-primary mt-auto">Add Loan</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">📄 All Loans</h5>
                    <p class="card-text flex-grow-1">Browse, update, or delete all active loans in the system.</p>
                    <a href="/loan-tracker/views/dashboard/loans_table.php" class="btn btn-secondary mt-auto">View Loans</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">📊 Reports</h5>
                    <p class="card-text flex-grow-1">Generate reports and visualize profits with charts. Export to PDF or Excel.</p>
                    <a href="/loan-tracker/views/dashboard/reports.php" class="btn btn-success mt-auto">Generate Reports</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">📈 Interest vs Principal</h5>
                    <canvas id="interestChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">🧾 Loan Status</h5>
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h5 class="card-title">🕒 Recent Activity</h5>
            <ul class="list-group list-group-flush">
                <?php foreach ($recentLoans as $loan): ?>
                    <li class="list-group-item">
                        <strong><?= htmlspecialchars($loan['borrower_name'] ?? 'Unknown') ?></strong>
                        borrowed <strong>K<?= number_format($loan['loan_amount'], 2) ?></strong>
                        on <?= date('M d, Y', strtotime($loan['start_date'])) ?> —
                        <span class="badge bg-<?= $loan['status'] === 'Paid' ? 'success' : 'danger' ?>">
                            <?= $loan['status'] ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Inline JS for Chart.js -->
<script>
    const interestData = {
        labels: ["Total Payable", "Interest Earned"],
        datasets: [{
            label: 'Amount (K)',
            data: [
                <?= number_format(array_sum(array_column($loans, 'total_payable')), 2) ?>,
                <?= number_format($totalInterest, 2) ?>
            ],
            backgroundColor: ['#0066cc', '#28a745']
        }]
    };

    const statusData = {
        labels: ['Paid', 'Unpaid'],
        datasets: [{
            label: 'Loans',
            data: [
                <?= count(array_filter($loans, fn($l) => $l['status'] === 'Paid')) ?>,
                <?= $totalUnpaid ?>
            ],
            backgroundColor: ['#198754', '#dc3545']
        }]
    };

    new Chart(document.getElementById('interestChart'), {
        type: 'bar',
        data: interestData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: statusData,
        options: {
            responsive: true
        }
    });
</script>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>