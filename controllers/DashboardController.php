<?php

require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/PrescriptionModel.php';

class DashboardController
{
    public function index()
    {
        Auth::requireRole('admin', 'doctor', 'patient');

        $role = Auth::role();

        if ($role === 'admin') {
            $this->admin();
            return;
        }

        if ($role === 'doctor') {
            $this->doctor();
            return;
        }

        if ($role === 'patient') {
            $this->patient();
            return;
        }

        redirect('index.php?page=error&action=403');
    }

    private function admin()
    {
        Auth::requireRole('admin');

        $userModel = new UserModel();
        $appointmentModel = new AppointmentModel();

        $stats = [
            'admins' => $userModel->countAll('admin'),
            'doctors' => $userModel->countAll('doctor'),
            'patients' => $userModel->countAll('patient'),
            'appointments' => $appointmentModel->countFiltered('all', 0, [])
        ];

        $recentAppointments = $appointmentModel->getRecent(5);

        require_once __DIR__ . '/../views/dashboard/admin.php';
    }

    private function doctor()
    {
        Auth::requireRole('doctor');

        $doctorModel = new DoctorModel();
        $appointmentModel = new AppointmentModel();

        $doctor = $doctorModel->findByUserId(Auth::currentUser()['id']);

        $todayAppointments = [];
        $appointments = [];

        if ($doctor) {
            $todayAppointments = $appointmentModel->getTodayByDoctor($doctor['id']);
            $appointments = $appointmentModel->getByDoctor($doctor['id'], 1, []);
        }

        require_once __DIR__ . '/../views/dashboard/doctor.php';
    }

    private function patient()
    {
        Auth::requireRole('patient');

        $appointmentModel = new AppointmentModel();
        $prescriptionModel = new PrescriptionModel();

        $appointments = $appointmentModel->getByPatient(Auth::currentUser()['id'], 1, []);
        $prescriptions = $prescriptionModel->getByPatient(Auth::currentUser()['id']);

        require_once __DIR__ . '/../views/dashboard/patient.php';
    }
}