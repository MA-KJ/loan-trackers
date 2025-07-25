<?php
class Loan
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function addLoan($borrowerId, $loanAmount, $interestRate, $durationDays, $adminId)
    {
        $startDate = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime("+$durationDays days"));
        $totalPayable = $loanAmount + ($loanAmount * ($interestRate / 100));

        $stmt = $this->pdo->prepare("INSERT INTO loans
            (borrower_id, loan_amount, interest_rate, total_payable, duration_days, start_date, due_date, status, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Unpaid', ?)");
        return $stmt->execute([$borrowerId, $loanAmount, $interestRate, $totalPayable, $durationDays, $startDate, $dueDate, $adminId]);
    }

    public function getAllLoans()
    {
        $stmt = $this->pdo->query("SELECT l.*, b.full_name FROM loans l
                                   JOIN borrowers b ON l.borrower_id = b.borrower_id
                                   ORDER BY l.due_date ASC");
        return $stmt->fetchAll();
    }

    public function markAsPaid($loanId)
    {
        $stmt = $this->pdo->prepare("UPDATE loans SET status = 'Paid', paid_date = CURDATE() WHERE loan_id = ?");
        return $stmt->execute([$loanId]);
    }

    public function deleteLoan($loanId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM loans WHERE loan_id = ?");
        return $stmt->execute([$loanId]);
    }

    public function getLoanById($loanId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM loans WHERE loan_id = ?");
        $stmt->execute([$loanId]);
        return $stmt->fetch();
    }
}
