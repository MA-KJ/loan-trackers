<?php
require_once __DIR__ . '/../models/Loan.php';
require_once __DIR__ . '/../models/Borrower.php';

class LoanController
{
    private $loanModel;
    private $borrowerModel;

    public function __construct($pdo)
    {
        $this->loanModel = new Loan($pdo);
        $this->borrowerModel = new Borrower($pdo);
    }

    public function createLoan($fullName, $loanAmount, $interestRate, $durationDays, $adminId)
    {
        $borrowerId = $this->borrowerModel->getOrCreateBorrower($fullName);
        return $this->loanModel->addLoan($borrowerId, $loanAmount, $interestRate, $durationDays, $adminId);
    }

    public function markLoanAsPaid($loanId)
    {
        return $this->loanModel->markAsPaid($loanId);
    }

    public function deleteLoan($loanId)
    {
        return $this->loanModel->deleteLoan($loanId);
    }

    public function listLoans()
    {
        return $this->loanModel->getAllLoans();
    }

    public function getLoanDetails($loanId)
    {
        return $this->loanModel->getLoanById($loanId);
    }

    public function getAllLoans()
    {
        return $this->loanModel->getAllLoans();
    }
}
