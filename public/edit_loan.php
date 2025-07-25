<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Loan.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$loanModel = new Loan($pdo);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loanId = $_POST['loan_id'];
    $amount = floatval($_POST['loan_amount']);
    $rate = floatval($_POST['interest_rate']);
    $duration = intval($_POST['duration_days']);

    $loan = $loanModel->getLoanById($loanId);
    if ($loan) {
        $totalPayable = $amount + ($amount * ($rate / 100));
        $dueDate = date('Y-m-d', strtotime($loan['start_date'] . " +{$duration} days"));

        $stmt = $pdo->prepare("UPDATE loans SET loan_amount = ?, interest_rate = ?, total_payable = ?, duration_days = ?, due_date = ? WHERE loan_id = ?");
        $stmt->execute([$amount, $rate, $totalPayable, $duration, $dueDate, $loanId]);

        $message = "Loan updated successfully.";
    }
} else if (isset($_GET['id'])) {
    $loan = $loanModel->getLoanById($_GET['id']);
}

$pageTitle = 'Edit Loan';
include __DIR__ . '/../views/layout/header.php';
?>

<h3>Edit Loan</h3>
<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if (isset($loan)): ?>
    <form method="post">
        <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">
        <label>Loan Amount: <input type="number" step="0.01" name="loan_amount" value="<?= $loan['loan_amount'] ?>" required></label><br>
        <label>Interest Rate (%): <input type="number" step="0.01" name="interest_rate" value="<?= $loan['interest_rate'] ?>" required></label><br>
        <label>Duration (days): <input type="number" name="duration_days" value="<?= $loan['duration_days'] ?>" required></label><br>
        <button type="submit">Update Loan</button>
    </form>
<?php else: ?>
    <p>Loan not found.</p>
<?php endif; ?>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>