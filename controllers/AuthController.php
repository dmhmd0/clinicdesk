<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if (Auth::check()) {
            redirect('index.php?page=dashboard');
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function doLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=auth&action=login');
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid security token.');
            redirect('index.php?page=auth&action=login');
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            setFlash('danger', 'Invalid credentials.');
            redirect('index.php?page=auth&action=login');
        }

        if ((int)$user['is_active'] !== 1) {
            setFlash('danger', 'Account suspended. Contact admin.');
            redirect('index.php?page=auth&action=login');
        }

        Auth::login($user);
        redirect('index.php?page=dashboard');
    }

    public function logout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=dashboard');
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid security token.');
            redirect('index.php?page=dashboard');
        }

        Auth::logout();
        redirect('index.php?page=auth&action=login');
    }
}