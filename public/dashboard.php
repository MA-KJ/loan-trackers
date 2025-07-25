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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
    <a href="logout.php">Logout</a>
    <h3>Loan Summary</h3>
    <a href="../views/dashboard/add_loan.php">Add Loan</a> |
    <a href="../views/dashboard/reports.php">Reports</a> |
    <a href="../views/dashboard/loans_table.php">View Loans</a>
</body>

</html>