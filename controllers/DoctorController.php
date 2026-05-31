<?php

require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/SpecializationModel.php';
require_once __DIR__ . '/../core/Paginator.php';
require_once __DIR__ . '/../config/config.php';

class DoctorController
{
    private $doctorModel;
    private $specializationModel;

    public function __construct()
    {
        Auth::requireRole('admin', 'doctor');
        $this->doctorModel = new DoctorModel();
        $this->specializationModel = new SpecializationModel();
    }

    public function index()
    {
        Auth::requireRole('admin');
        $page = max(1, (int)($_GET['p'] ?? 1));
        $doctors = $this->doctorModel->getAllPaginated($page);
        $total = $this->doctorModel->countAll();
        $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
        require_once __DIR__ . '/../views/doctors/index.php';
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);

        if (Auth::role() === 'doctor') {
            $doctor = $this->doctorModel->findByUserId(Auth::currentUser()['id']);
        } else {
            $doctor = $this->doctorModel->findById($id);
        }

        if (!$doctor) {
            require_once __DIR__ . '/../views/errors/404.php';
            return;
        }

        $specializations = $this->specializationModel->getAll();
        require_once __DIR__ . '/../views/doctors/edit.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=doctors');
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid security token.');
            redirect('index.php?page=doctors');
        }

        $doctorId = (int)($_POST['id'] ?? 0);
        $doctor = $this->doctorModel->findById($doctorId);

        if (!$doctor) {
            setFlash('danger', 'Doctor not found.');
            redirect('index.php?page=doctors');
        }

        if (Auth::role() === 'doctor') {
            $ownDoctor = $this->doctorModel->findByUserId(Auth::currentUser()['id']);
            if (!$ownDoctor || (int)$ownDoctor['id'] !== $doctorId) {
                require_once __DIR__ . '/../views/errors/403.php';
                return;
            }
        }

        $days = $_POST['available_days'] ?? [];
        if (empty($days)) {
            setFlash('danger', 'Select at least one available day.');
            redirect('index.php?page=doctors&action=edit&id=' . $doctorId);
        }

        $this->doctorModel->update($doctorId, [
            'specialization_id' => (int)($_POST['specialization_id'] ?? 0),
            'bio' => trim($_POST['bio'] ?? ''),
            'consultation_fee' => (float)($_POST['consultation_fee'] ?? 0),
            'available_days' => implode(',', $days)
        ]);

        setFlash('success', 'Doctor updated successfully.');
        redirect(Auth::role() === 'admin' ? 'index.php?page=doctors' : 'index.php?page=dashboard');
    }

    public function specializations()
    {
        Auth::requireRole('admin');
        $specializations = $this->specializationModel->getAll();
        require_once __DIR__ . '/../views/doctors/specializations.php';
    }

    public function storeSpecialization()
    {
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=doctors&action=specializations');
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid security token.');
            redirect('index.php?page=doctors&action=specializations');
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            setFlash('danger', 'Specialization name is required.');
            redirect('index.php?page=doctors&action=specializations');
        }

        $this->specializationModel->create($name);
        setFlash('success', 'Specialization added successfully.');
        redirect('index.php?page=doctors&action=specializations');
    }

    public function deleteSpecialization()
    {
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=doctors&action=specializations');
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid security token.');
            redirect('index.php?page=doctors&action=specializations');
        }

        $id = (int)($_POST['id'] ?? 0);

        if (!$this->specializationModel->isSafeToDelete($id)) {
            setFlash('danger', 'Cannot delete this specialization because doctors use it.');
            redirect('index.php?page=doctors&action=specializations');
        }

        $this->specializationModel->delete($id);
        setFlash('success', 'Specialization deleted successfully.');
        redirect('index.php?page=doctors&action=specializations');
    }
}
