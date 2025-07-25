<?php
require_once __DIR__ . '/../vendor/autoload.php'; // PhpSpreadsheet
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/LoanController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$loanController = new LoanController($pdo);
$loans = $loanController->getAllLoans();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Loans');

$sheet->fromArray([
    ['Borrower', 'Loan', 'Interest %', 'Total', 'Duration', 'Start Date', 'Due Date', 'Status']
], NULL, 'A1');

$row = 2;
foreach ($loans as $loan) {
    $sheet->fromArray([
        $loan['full_name'],
        $loan['loan_amount'],
        $loan['interest_rate'],
        $loan['total_payable'],
        $loan['duration_days'],
        $loan['start_date'],
        $loan['due_date'],
        $loan['status']
    ], NULL, 'A' . $row++);
}

$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="loan_report.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
