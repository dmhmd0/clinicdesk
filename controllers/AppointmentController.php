<?php

require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../core/Paginator.php';
require_once __DIR__ . '/../config/config.php';

class AppointmentController
{
    private $appointmentModel;
    private $doctorModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->doctorModel = new DoctorModel();
    }

    public function index()
    {
        Auth::requireRole('admin', 'doctor', 'patient');

        $role = Auth::role();
        $page = max(1, (int)($_GET['p'] ?? 1));

        $filters = [
            'status' => $_GET['status'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? '',
            'doctor_id' => $_GET['doctor_id'] ?? '',
            'patient_name' => $_GET['patient_name'] ?? ''
        ];

        $doctors = $this->doctorModel->getAll();
        $appointments = [];
        $total = 0;
        $doctor = null;

        if ($role === 'admin') {
            $appointments = $this->appointmentModel->getAll($page, $filters);
            $total = $this->appointmentModel->countFiltered('all', 0, $filters);
        }

        if ($role === 'doctor') {
            $doctor = $this->doctorModel->findByUserId(Auth::currentUser()['id']);
            if ($doctor) {
                $appointments = $this->appointmentModel->getByDoctor($doctor['id'], $page, $filters);
                $total = $this->appointmentModel->countFiltered('doctor', $doctor['id'], $filters);
            }
        }

        if ($role === 'patient') {
            $appointments = $this->appointmentModel->getByPatient(Auth::currentUser()['id'], $page, $filters);
            $total = $this->appointmentModel->countFiltered('patient', Auth::currentUser()['id'], $filters);
        }

        $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);

        require_once __DIR__ . '/../views/appointments/index.php';
    }

    public function book()
    {
        Auth::requireRole('patient');

        $doctors = $this->doctorModel->getAll();
        require_once __DIR__ . '/../views/appointments/book.php';
    }

    public function store()
    {
        Auth::requireRole('patient');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=appointments&action=book');
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid security token.');
            redirect('index.php?page=appointments&action=book');
        }

        $doctorId = (int)($_POST['doctor_id'] ?? 0);
        $date = $_POST['appt_date'] ?? '';
        $time = $_POST['appt_time'] ?? '';
        $reason = trim($_POST['reason'] ?? '');

        if ($doctorId <= 0 || $date === '' || $time === '') {
            setFlash('danger', 'Doctor, date and time are required.');
            redirect('index.php?page=appointments&action=book');
        }

        if ($date < date('Y-m-d')) {
            setFlash('danger', 'Appointment date cannot be in the past.');
            redirect('index.php?page=appointments&action=book');
        }

        $availableDays = $this->doctorModel->getAvailableDays($doctorId);
        $dayName = date('D', strtotime($date));

        if (!in_array($dayName, $availableDays)) {
            setFlash('danger', 'Selected doctor is not available on this day.');
            redirect('index.php?page=appointments&action=book');
        }

        if ($this->appointmentModel->hasConflict($doctorId, $date, $time)) {
            setFlash('danger', 'This slot is already booked, please choose another time.');
            redirect('index.php?page=appointments&action=book');
        }

        $saved = $this->appointmentModel->book([
            'patient_id' => Auth::currentUser()['id'],
            'doctor_id' => $doctorId,
            'appt_date' => $date,
            'appt_time' => $time,
            'reason' => $reason
        ]);

        if (!$saved) {
            setFlash('danger', 'Appointment could not be booked.');
            redirect('index.php?page=appointments&action=book');
        }

        setFlash('success', 'Appointment booked successfully.');
        redirect('index.php?page=appointments');
    }

    public function show()
    {
        Auth::requireRole('admin', 'doctor', 'patient');

        $id = (int)($_GET['id'] ?? 0);
        $appointment = $this->appointmentModel->findById($id);

        if (!$appointment) {
            require_once __DIR__ . '/../views/errors/404.php';
            return;
        }

        if (!$this->canAccessAppointment($appointment)) {
            require_once __DIR__ . '/../views/errors/403.php';
            return;
        }

        require_once __DIR__ . '/../views/appointments/show.php';
    }

    public function updateStatus()
    {
        Auth::requireRole('admin', 'doctor', 'patient');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=appointments');
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid security token.');
            redirect('index.php?page=appointments');
        }

        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $notes = trim($_POST['doctor_notes'] ?? '');

        $allowed = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $allowed)) {
            setFlash('danger', 'Invalid appointment status.');
            redirect('index.php?page=appointments');
        }

        $appointment = $this->appointmentModel->findById($id);
        if (!$appointment) {
            setFlash('danger', 'Appointment not found.');
            redirect('index.php?page=appointments');
        }

        $role = Auth::role();

        if ($role === 'patient') {
            if ((int)$appointment['patient_id'] !== (int)Auth::currentUser()['id']) {
                require_once __DIR__ . '/../views/errors/403.php';
                return;
            }

            if ($status !== 'cancelled' || $appointment['status'] !== 'pending') {
                setFlash('danger', 'Patients can only cancel pending appointments.');
                redirect('index.php?page=appointments');
            }
        }

        if ($role === 'doctor') {
            $doctor = $this->doctorModel->findByUserId(Auth::currentUser()['id']);

            if (!$doctor || (int)$appointment['doctor_id'] !== (int)$doctor['id']) {
                require_once __DIR__ . '/../views/errors/403.php';
                return;
            }

            $validDoctorChange =
                ($appointment['status'] === 'pending' && in_array($status, ['confirmed', 'cancelled'])) ||
                ($appointment['status'] === 'confirmed' && in_array($status, ['completed', 'cancelled']));

            if (!$validDoctorChange) {
                setFlash('danger', 'Invalid status change.');
                redirect('index.php?page=appointments');
            }
        }

        $this->appointmentModel->updateStatus($id, $status, $notes);
        setFlash('success', 'Appointment status updated successfully.');
        redirect('index.php?page=appointments&action=show&id=' . $id);
    }

    private function canAccessAppointment($appointment)
    {
        $role = Auth::role();

        if ($role === 'admin') {
            return true;
        }

        if ($role === 'patient') {
            return (int)$appointment['patient_id'] === (int)Auth::currentUser()['id'];
        }

        if ($role === 'doctor') {
            $doctor = $this->doctorModel->findByUserId(Auth::currentUser()['id']);
            return $doctor && (int)$appointment['doctor_id'] === (int)$doctor['id'];
        }

        return false;
    }
}
