<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/LoanController.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$loanController = new LoanController($pdo);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name']);
    $amount = floatval($_POST['loan_amount']);
    $rate = floatval($_POST['interest_rate']);
    $duration = intval($_POST['duration_days']);

    if ($loanController->createLoan($name, $amount, $rate, $duration, $_SESSION['user_id'])) {
        $message = "Loan successfully added.";
    } else {
        $message = "Error adding loan.";
    }
}
$pageTitle = 'Add Loan';
include __DIR__ . '/../layout/header.php';
?>
<h3>Add New Loan</h3>
<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>
<form method="post">
    <label>Full Name: <input type="text" name="full_name" required></label><br>
    <label>Loan Amount: <input type="number" name="loan_amount" step="0.01" required></label><br>
    <label>Interest Rate (%): <input type="number" name="interest_rate" step="0.01" required></label><br>
    <label>Duration (days): <input type="number" name="duration_days" required></label><br>
    <button type="submit">Create Loan</button>
</form>
<?php include __DIR__ . '/../layout/footer.php'; ?>