<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Dompdf
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ReportController.php';

use Dompdf\Dompdf;

$reportController = new ReportController($pdo);
$summary = $reportController->generateSummary(date('Y-m-d', strtotime('-3 months')), date('Y-m-d'));

$html = "<h1>Loan Report</h1>
<p>Total Loans: {$summary['loan_count']}</p>
<p>Total Lent: K{$summary['total_lent']}</p>
<p>Total Interest: K{$summary['interest_earned']}</p>
<p>Total Repaid: K{$summary['repaid']}</p>
<p>Total Unpaid: K{$summary['unpaid']}</p>";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('loan_report.pdf', ["Attachment" => true]);
