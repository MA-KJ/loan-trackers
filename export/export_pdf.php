<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ReportController.php';

use Dompdf\Dompdf;

// Handle filters sent via POST
$filters = [
    'borrower' => $_POST['borrower'] ?? '',
    'status'   => $_POST['status'] ?? '',
    'from'     => $_POST['from'] ?? '',
    'to'       => $_POST['to'] ?? ''
];

// Fetch filtered loans using controller
$reportController = new ReportController($pdo);
$loans = $reportController->getFilteredLoans($filters);

// Generate HTML table
$html = "<h2 style='text-align:center;'>Loan Report</h2>";
$html .= "<table border='1' cellspacing='0' cellpadding='5' width='100%'>
    <thead>
        <tr style='background-color:#f0f0f0;'>
            <th>Borrower</th>
            <th>Amount (K)</th>
            <th>Rate (%)</th>
            <th>Total Payable (K)</th>
            <th>Status</th>
            <th>Start Date</th>
            <th>Due Date</th>
        </tr>
    </thead>
    <tbody>";

foreach ($loans as $loan) {
    $html .= "<tr>
        <td>" . htmlspecialchars($loan['full_name']) . "</td>
        <td>K" . number_format($loan['loan_amount'], 2) . "</td>
        <td>" . htmlspecialchars($loan['interest_rate']) . "%</td>
        <td>K" . number_format($loan['total_payable'], 2) . "</td>
        <td>" . htmlspecialchars($loan['status']) . "</td>
        <td>" . htmlspecialchars($loan['start_date']) . "</td>
        <td>" . htmlspecialchars($loan['due_date']) . "</td>
    </tr>";
}

$html .= "</tbody></table>";

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("loan_report.pdf", ["Attachment" => false]);
