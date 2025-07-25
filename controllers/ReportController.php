<?php
require_once __DIR__ . '/../models/Loan.php';

class ReportController
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function generateSummary($startDate, $endDate)
    {
        $loan = new Loan($this->pdo);
        return $loan->getSummaryBetween($startDate, $endDate);
    }
    public function generateFilteredSummary($filters)
    {
        // Main Summary SQL
        $sql = "
        SELECT 
            COUNT(*) AS loan_count,
            SUM(loan_amount) AS total_lent,
            SUM(total_payable - loan_amount) AS interest_earned,
            SUM(CASE WHEN status = 'Paid' THEN total_payable ELSE 0 END) AS repaid,
            SUM(CASE WHEN status = 'Unpaid' THEN total_payable ELSE 0 END) AS unpaid,
            SUM(CASE WHEN status = 'Paid' THEN 1 ELSE 0 END) AS paid_loans,
            SUM(CASE WHEN status = 'Unpaid' THEN 1 ELSE 0 END) AS unpaid_loans
        FROM loans
        JOIN borrowers ON loans.borrower_id = borrowers.borrower_id
        WHERE 1=1
    ";

        $params = [];

        if (!empty($filters['borrower'])) {
            $sql .= " AND borrowers.full_name LIKE :borrower";
            $params[':borrower'] = '%' . $filters['borrower'] . '%';
        }
        if (!empty($filters['status'])) {
            $sql .= " AND loans.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['from']) && !empty($filters['to'])) {
            $sql .= " AND loans.start_date BETWEEN :from AND :to";
            $params[':from'] = $filters['from'];
            $params[':to'] = $filters['to'];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);

        // ✅ Get interest by month
        $chartSql = "
        SELECT 
            DATE_FORMAT(start_date, '%b') AS month_label,
            SUM(total_payable - loan_amount) AS interest
        FROM loans
        JOIN borrowers ON loans.borrower_id = borrowers.borrower_id
        WHERE start_date BETWEEN :from AND :to
    ";

        if (!empty($filters['borrower'])) {
            $chartSql .= " AND borrowers.full_name LIKE :borrower";
        }
        if (!empty($filters['status'])) {
            $chartSql .= " AND loans.status = :status";
        }

        $chartSql .= " GROUP BY MONTH(start_date)
                   ORDER BY MONTH(start_date)";

        $stmtChart = $this->pdo->prepare($chartSql);
        $stmtChart->execute($params);

        $months = [];
        $interest = [];
        foreach ($stmtChart->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $months[] = $row['month_label'];
            $interest[] = round($row['interest'], 2);
        }

        $summary['months'] = $months;
        $summary['interest'] = $interest;

        // ✅ Top Borrowers by Interest Earned
        $topSql = "
        SELECT borrowers.full_name, 
               SUM(total_payable - loan_amount) AS total_interest
        FROM loans
        JOIN borrowers ON loans.borrower_id = borrowers.borrower_id
        WHERE 1=1
    ";

        if (!empty($filters['borrower'])) {
            $topSql .= " AND borrowers.full_name LIKE :borrower";
        }
        if (!empty($filters['status'])) {
            $topSql .= " AND loans.status = :status";
        }
        if (!empty($filters['from']) && !empty($filters['to'])) {
            $topSql .= " AND loans.start_date BETWEEN :from AND :to";
        }

        $topSql .= "
        GROUP BY borrowers.full_name
        ORDER BY total_interest DESC
        LIMIT 5
    ";

        $stmtTop = $this->pdo->prepare($topSql);
        $stmtTop->execute($params);
        $summary['top_borrowers'] = $stmtTop->fetchAll(PDO::FETCH_ASSOC);

        return $summary;
    }


    public function getFilteredLoans($filters)
    {
        $sql = "
        SELECT loans.*, borrowers.full_name
        FROM loans
        JOIN borrowers ON loans.borrower_id = borrowers.borrower_id
        WHERE 1=1
    ";

        $params = [];

        if (!empty($filters['borrower'])) {
            $sql .= " AND borrowers.full_name LIKE :borrower";
            $params[':borrower'] = '%' . $filters['borrower'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND loans.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['from']) && !empty($filters['to'])) {
            $sql .= " AND loans.start_date BETWEEN :from AND :to";
            $params[':from'] = $filters['from'];
            $params[':to'] = $filters['to'];
        }

        $sql .= " ORDER BY loans.start_date DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
