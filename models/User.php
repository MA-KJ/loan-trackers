<?php
class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function login($username, $password)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return false;
    }

    public function createAdmin($username, $password, $role = 'admin')
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $hash, $role]);
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
