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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $range = $_POST['range'] ?? '3';
    $months = intval($range);
    $end = date('Y-m-d');
    $start = date('Y-m-d', strtotime("-$months months"));
    $summary = $reportController->generateSummary($start, $end);
}
$pageTitle = 'Reports';
include __DIR__ . '/../layout/header.php';
?>
<h3>Generate Report</h3>
<form method="post">
    <label>Select Range:
        <select name="range">
            <option value="3">Last 3 Months</option>
            <option value="6">Last 6 Months</option>
            <option value="9">Last 9 Months</option>
            <option value="12">Last 12 Months</option>
        </select>
    </label>
    <button type="submit">Generate</button>
</form>

<?php if ($summary): ?>
    <h4>Report Summary:</h4>
    <ul>
        <li>Total Loans: <?= $summary['loan_count'] ?></li>
        <li>Total Capital Lent: K<?= number_format($summary['total_lent'], 2) ?></li>
        <li>Total Interest Earned: K<?= number_format($summary['interest_earned'], 2) ?></li>
        <li>Total Repaid: K<?= number_format($summary['repaid'], 2) ?></li>
        <li>Total Unpaid: K<?= number_format($summary['unpaid'], 2) ?></li>
    </ul>
<?php endif; ?>
<?php include __DIR__ . '/../layout/footer.php'; ?>