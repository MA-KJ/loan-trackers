<?php
require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private $userModel;

    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
    }

    public function login($username, $password)
    {
        $user = $this->userModel->login($username, $password);
        if ($user) {
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
        return false;
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
    }

    public function registerAdmin($username, $password, $role = 'admin')
    {
        return $this->userModel->createAdmin($username, $password, $role);
    }
}
