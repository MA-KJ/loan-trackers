<?php
require_once __DIR__ . '/../models/Loan.php';

class ReportController
{
    public $loanModel;

    public function __construct($pdo)
    {
        $this->loanModel = new Loan($pdo);
    }

    public function generateSummary($startDate, $endDate)
    {
        $sql = "SELECT 
                    COUNT(*) AS loan_count,
                    SUM(loan_amount) AS total_lent,
                    SUM(CASE WHEN status = 'Paid' THEN total_payable - loan_amount ELSE 0 END) AS interest_earned,
                    SUM(CASE WHEN status = 'Paid' THEN loan_amount ELSE 0 END) AS repaid,
                    SUM(CASE WHEN status = 'Unpaid' THEN loan_amount ELSE 0 END) AS unpaid
                FROM loans 
                WHERE start_date BETWEEN ? AND ?";

        $stmt = $this->loanModel->pdo->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetch();
    }
}
