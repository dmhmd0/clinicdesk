<?php

require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/PrescriptionModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../config/config.php';

class PrescriptionController
{
    private $appointmentModel;
    private $prescriptionModel;
    private $doctorModel;

    public function __construct()
    {
        Auth::requireRole('admin', 'doctor', 'patient');
        $this->appointmentModel = new AppointmentModel();
        $this->prescriptionModel = new PrescriptionModel();
        $this->doctorModel = new DoctorModel();
    }

    public function index()
    {
        Auth::requireRole('patient');
        $prescriptions = $this->prescriptionModel->getByPatient(Auth::currentUser()['id']);
        require_once __DIR__ . '/../views/prescriptions/index.php';
    }

    public function create()
    {
        Auth::requireRole('doctor');
        $appointmentId = (int)($_GET['appointment_id'] ?? 0);
        $appointment = $this->appointmentModel->findById($appointmentId);

        if (!$appointment || !$this->doctorCanUseAppointment($appointment)) {
            require_once __DIR__ . '/../views/errors/403.php';
            return;
        }

        if ($appointment['status'] !== 'completed' || !empty($appointment['prescription_id'])) {
            setFlash('danger', 'Prescription can only be added to completed appointment without prescription.');
            redirect('index.php?page=appointments&action=show&id=' . $appointmentId);
        }

        require_once __DIR__ . '/../views/prescriptions/add.php';
    }

    public function store()
    {
        Auth::requireRole('doctor');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=appointments');
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid security token.');
            redirect('index.php?page=appointments');
        }

        $appointmentId = (int)($_POST['appointment_id'] ?? 0);
        $appointment = $this->appointmentModel->findById($appointmentId);

        if (!$appointment || !$this->doctorCanUseAppointment($appointment)) {
            require_once __DIR__ . '/../views/errors/403.php';
            return;
        }

        if ($appointment['status'] !== 'completed' || !empty($appointment['prescription_id'])) {
            setFlash('danger', 'Invalid prescription request.');
            redirect('index.php?page=appointments&action=show&id=' . $appointmentId);
        }

        $diagnosis = trim($_POST['diagnosis'] ?? '');
        $medications = trim($_POST['medications'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if ($diagnosis === '' || $medications === '') {
            setFlash('danger', 'Diagnosis and medications are required.');
            redirect('index.php?page=prescriptions&action=create&appointment_id=' . $appointmentId);
        }

        $filePath = null;

        if (!empty($_FILES['prescription_file']['name'])) {
            if ($_FILES['prescription_file']['error'] !== UPLOAD_ERR_OK) {
                setFlash('danger', 'File upload failed.');
                redirect('index.php?page=prescriptions&action=create&appointment_id=' . $appointmentId);
            }

            if ($_FILES['prescription_file']['size'] > MAX_PDF_SIZE) {
                setFlash('danger', 'PDF file must be 3 MB or less.');
                redirect('index.php?page=prescriptions&action=create&appointment_id=' . $appointmentId);
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['prescription_file']['tmp_name']);
            finfo_close($finfo);

            if ($mime !== 'application/pdf') {
                setFlash('danger', 'Only PDF files are allowed.');
                redirect('index.php?page=prescriptions&action=create&appointment_id=' . $appointmentId);
            }

            $fileName = 'prescription_' . $appointmentId . '_' . time() . '.pdf';
            $uploadDir = __DIR__ . '/../public/uploads/prescriptions/';
            $target = $uploadDir . $fileName;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (!move_uploaded_file($_FILES['prescription_file']['tmp_name'], $target)) {
                setFlash('danger', 'Could not save uploaded file.');
                redirect('index.php?page=prescriptions&action=create&appointment_id=' . $appointmentId);
            }

            $filePath = 'public/uploads/prescriptions/' . $fileName;
        }

        $this->prescriptionModel->create([
            'appointment_id' => $appointmentId,
            'diagnosis' => $diagnosis,
            'medications' => $medications,
            'notes' => $notes,
            'file_path' => $filePath
        ]);

        setFlash('success', 'Prescription added successfully.');
        redirect('index.php?page=appointments&action=show&id=' . $appointmentId);
    }

    public function download()
    {
        Auth::requireRole('admin', 'doctor', 'patient');

        $appointmentId = (int)($_GET['id'] ?? 0);
        $appointment = $this->appointmentModel->findById($appointmentId);
        $prescription = $this->prescriptionModel->findByAppointmentId($appointmentId);

        if (!$appointment || !$prescription || empty($prescription['file_path'])) {
            setFlash('danger', 'Prescription file not found.');
            redirect('index.php?page=appointments');
        }

        if (!$this->canDownload($appointment)) {
            require_once __DIR__ . '/../views/errors/403.php';
            return;
        }

        $path = __DIR__ . '/../' . $prescription['file_path'];
        if (!file_exists($path)) {
            setFlash('danger', 'File is missing on disk.');
            redirect('index.php?page=appointments');
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="prescription.pdf"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    private function doctorCanUseAppointment($appointment)
    {
        $doctor = $this->doctorModel->findByUserId(Auth::currentUser()['id']);
        return $doctor && (int)$appointment['doctor_id'] === (int)$doctor['id'];
    }

    private function canDownload($appointment)
    {
        if (Auth::role() === 'admin') {
            return true;
        }

        if (Auth::role() === 'patient') {
            return (int)$appointment['patient_id'] === (int)Auth::currentUser()['id'];
        }

        if (Auth::role() === 'doctor') {
            return $this->doctorCanUseAppointment($appointment);
        }

        return false;
    }
}
