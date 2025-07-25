<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ReportController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Handle filters sent via POST
$filters = [
    'borrower' => $_POST['borrower'] ?? '',
    'status'   => $_POST['status'] ?? '',
    'from'     => $_POST['from'] ?? '',
    'to'       => $_POST['to'] ?? ''
];

// Fetch filtered data
$reportController = new ReportController($pdo);
$loans = $reportController->getFilteredLoans($filters);

// Create spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set headers
$sheet->setCellValue('A1', 'Borrower');
$sheet->setCellValue('B1', 'Loan Amount');
$sheet->setCellValue('C1', 'Interest Rate (%)');
$sheet->setCellValue('D1', 'Total Payable');
$sheet->setCellValue('E1', 'Status');
$sheet->setCellValue('F1', 'Start Date');
$sheet->setCellValue('G1', 'Due Date');

// Fill rows
$row = 2;
foreach ($loans as $loan) {
    $sheet->setCellValue('A' . $row, $loan['full_name']);
    $sheet->setCellValue('B' . $row, $loan['loan_amount']);
    $sheet->setCellValue('C' . $row, $loan['interest_rate']);
    $sheet->setCellValue('D' . $row, $loan['total_payable']);
    $sheet->setCellValue('E' . $row, $loan['status']);
    $sheet->setCellValue('F' . $row, $loan['start_date']);
    $sheet->setCellValue('G' . $row, $loan['due_date']);
    $row++;
}

// Output as Excel download
$filename = "loan_report.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
