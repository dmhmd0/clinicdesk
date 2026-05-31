<?php

require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/SpecializationModel.php';

class UserController
{
    private $userModel;
    private $doctorModel;
    private $specializationModel;

    public function __construct()
    {
        Auth::requireRole('admin');

        $this->userModel = new UserModel();
        $this->doctorModel = new DoctorModel();
        $this->specializationModel = new SpecializationModel();
    }

    public function index()
    {
        $page = $_GET['p'] ?? 1;
        $role = $_GET['role'] ?? '';

        $users = $this->userModel->getAllPaginated($page, $role);
        $totalUsers = $this->userModel->countAll($role);

        require_once __DIR__ . '/../views/users/index.php';
    }

    public function create()
    {
        $specializations = $this->specializationModel->getAll();

        require_once __DIR__ . '/../views/users/create.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=users');
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid security token.');
            redirect('index.php?page=users&action=create');
        }

        $name = trim($_POST['name'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'patient';
        $phone = trim($_POST['phone'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            setFlash('danger', 'Name, email and password are required.');
            redirect('index.php?page=users&action=create');
        }

        if (!in_array($role, ['admin', 'doctor', 'patient'])) {
            setFlash('danger', 'Invalid role.');
            redirect('index.php?page=users&action=create');
        }

        if ($this->userModel->findByEmail($email)) {
            setFlash('danger', 'Email already exists.');
            redirect('index.php?page=users&action=create');
        }

        $userId = $this->userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => $role,
            'phone' => $phone
        ]);

        if (!$userId) {
            setFlash('danger', 'User could not be created.');
            redirect('index.php?page=users&action=create');
        }

        if ($role === 'doctor') {
            $specializationId = (int)($_POST['specialization_id'] ?? 0);
            $bio = trim($_POST['bio'] ?? '');
            $fee = (float)($_POST['consultation_fee'] ?? 0);
            $days = $_POST['available_days'] ?? [];

            if ($specializationId <= 0 || empty($days)) {
                setFlash('danger', 'Doctor specialization and available days are required.');
                redirect('index.php?page=users&action=create');
            }

            $this->doctorModel->create([
                'user_id' => $userId,
                'specialization_id' => $specializationId,
                'bio' => $bio,
                'consultation_fee' => $fee,
                'available_days' => implode(',', $days)
            ]);
        }

        setFlash('success', 'User created successfully.');
        redirect('index.php?page=users');
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->userModel->findById($id);

        if (!$user) {
            require_once __DIR__ . '/../views/errors/404.php';
            return;
        }

        require_once __DIR__ . '/../views/users/edit.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=users');
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid security token.');
            redirect('index.php?page=users');
        }

        $id = (int)($_POST['id'] ?? 0);
        $user = $this->userModel->findById($id);

        if (!$user) {
            setFlash('danger', 'User not found.');
            redirect('index.php?page=users');
        }

        $this->userModel->update($id, [
            'name' => trim($_POST['name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'avatar' => $user['avatar']
        ]);

        setFlash('success', 'User updated successfully.');
        redirect('index.php?page=users');
    }

    public function toggle()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=users');
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid security token.');
            redirect('index.php?page=users');
        }

        $id = (int)($_POST['id'] ?? 0);
        $currentUser = Auth::currentUser();

        if ($id === (int)$currentUser['id']) {
            setFlash('danger', 'You cannot deactivate your own account.');
            redirect('index.php?page=users');
        }

        $this->userModel->toggleActive($id);

        setFlash('success', 'User status changed successfully.');
        redirect('index.php?page=users');
    }
}