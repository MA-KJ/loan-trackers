<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/LoanController.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$loanController = new LoanController($pdo);
$loans = $loanController->getAllLoans();
$pageTitle = 'All Loans';
include __DIR__ . '/../layout/header.php';
?>
<h3>Loan Records</h3>
<a href="add_loan.php">➕ Add New Loan</a> |
<a href="reports.php">📊 Reports</a>
<table border="1" cellpadding="6">
    <thead>
        <tr>
            <th>Borrower</th>
            <th>Loan</th>
            <th>Interest</th>
            <th>Total</th>
            <th>Duration</th>
            <th>Start</th>
            <th>Due</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($loans as $loan): ?>
            <tr>
                <td><?= htmlspecialchars($loan['full_name']) ?></td>
                <td>K<?= number_format($loan['loan_amount'], 2) ?></td>
                <td><?= number_format($loan['interest_rate'], 2) ?>%</td>
                <td>K<?= number_format($loan['total_payable'], 2) ?></td>
                <td><?= $loan['duration_days'] ?> days</td>
                <td><?= $loan['start_date'] ?></td>
                <td><?= $loan['due_date'] ?></td>
                <td><?= $loan['status'] ?></td>
                <td>
                    <?php if ($loan['status'] === 'Unpaid'): ?>
                        <a href="/loan-tracker/public/mark_paid.php?id=<?= $loan['loan_id'] ?>" onclick="return confirm('Mark as paid?')">✅ Mark as paid</a>
                    <?php endif; ?>
                    <a href="/loan-tracker/public/edit_loan.php?id=<?= $loan['loan_id'] ?>">✏️ Edit</a>
                    <a href="/loan-tracker/public/delete_loan.php?id=<?= $loan['loan_id'] ?>" onclick="return confirm('Are you sure?')"> 🗑️ Delete</a>

                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/../layout/footer.php'; ?>