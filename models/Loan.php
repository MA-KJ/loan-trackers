<?php
class Loan
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function addLoan($borrowerName, $amount, $rate, $duration, $createdBy)
    {
        $totalPayable = $amount + ($amount * ($rate / 100));
        $startDate = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime("+$duration days"));

        // Insert borrower
        $stmt = $this->pdo->prepare("INSERT INTO borrowers (full_name) VALUES (:name)");
        $stmt->execute([':name' => $borrowerName]);
        $borrowerId = $this->pdo->lastInsertId();

        // Insert loan
        $stmt = $this->pdo->prepare("
            INSERT INTO loans 
            (borrower_id, loan_amount, interest_rate, total_payable, duration_days, start_date, due_date, created_by) 
            VALUES 
            (:borrower_id, :loan_amount, :interest_rate, :total_payable, :duration_days, :start_date, :due_date, :created_by)
        ");
        return $stmt->execute([
            ':borrower_id' => $borrowerId,
            ':loan_amount' => $amount,
            ':interest_rate' => $rate,
            ':total_payable' => $totalPayable,
            ':duration_days' => $duration,
            ':start_date' => $startDate,
            ':due_date' => $dueDate,
            ':created_by' => $createdBy
        ]);
    }

    public function getSummaryBetween($start, $end)
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) AS loan_count,
                SUM(loan_amount) AS total_lent,
                SUM(total_payable - loan_amount) AS interest_earned,
                SUM(CASE WHEN status = 'Paid' THEN total_payable ELSE 0 END) AS repaid,
                SUM(CASE WHEN status = 'Unpaid' THEN total_payable ELSE 0 END) AS unpaid
            FROM loans
            WHERE start_date BETWEEN :start AND :end
        ");
        $stmt->execute([':start' => $start, ':end' => $end]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllLoans()
    {
        $stmt = $this->pdo->query("
            SELECT loans.*, borrowers.full_name 
            FROM loans 
            JOIN borrowers ON loans.borrower_id = borrowers.borrower_id 
            ORDER BY start_date DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsPaid($loanId)
    {
        $stmt = $this->pdo->prepare("UPDATE loans SET status = 'Paid', paid_date = CURDATE() WHERE loan_id = :id");
        return $stmt->execute([':id' => $loanId]);
    }

    public function deleteLoan($loanId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM loans WHERE loan_id = :id");
        return $stmt->execute([':id' => $loanId]);
    }

    public function getLoanById($loanId)
    {
        $stmt = $this->pdo->prepare("
        SELECT l.*, b.full_name 
        FROM loans l
        JOIN borrowers b ON l.borrower_id = b.borrower_id
        WHERE l.loan_id = :loan_id
    ");
        $stmt->execute([':loan_id' => $loanId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
