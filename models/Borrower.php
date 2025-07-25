<?php
class Borrower
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getOrCreateBorrower($fullName)
    {
        $stmt = $this->pdo->prepare("SELECT borrower_id FROM borrowers WHERE full_name = ?");
        $stmt->execute([$fullName]);
        $borrower = $stmt->fetch();

        if ($borrower) {
            return $borrower['borrower_id'];
        } else {
            $insert = $this->pdo->prepare("INSERT INTO borrowers (full_name) VALUES (?)");
            $insert->execute([$fullName]);
            return $this->pdo->lastInsertId();
        }
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM borrowers ORDER BY full_name ASC");
        return $stmt->fetchAll();
    }
}
