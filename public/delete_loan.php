<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/LoanController.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $loanController = new LoanController($pdo);
    $loanController->deleteLoan($_GET['id']);
}

header("Location: ../views/dashboard/loans_table.php");
exit();
