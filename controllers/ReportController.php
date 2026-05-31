<?php

require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

class ReportController
{
    private $appointmentModel;
    private $doctorModel;

    public function __construct()
    {
        Auth::requireRole('admin');
        $this->appointmentModel = new AppointmentModel();
        $this->doctorModel = new DoctorModel();
    }

    public function index()
    {
        $doctors = $this->doctorModel->getAll();
        $rows = [];
        $statusCounts = [];

        $filters = [
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? '',
            'doctor_id' => $_GET['doctor_id'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];

        if ($filters['start_date'] !== '' || $filters['end_date'] !== '') {
            if ($filters['start_date'] === '' || $filters['end_date'] === '' || $filters['start_date'] > $filters['end_date']) {
                setFlash('danger', 'Start date and end date are required, and start date must be before end date.');
            } else {
                $rows = $this->appointmentModel->getReportRows($filters);
                foreach ($rows as $row) {
                    $status = $row['status'];
                    $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
                }

                if (($_GET['export'] ?? '') === 'csv') {
                    $this->exportCsv($rows);
                }
            }
        }

        require_once __DIR__ . '/../views/reports/index.php';
    }

    private function exportCsv($rows)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="appointments_report.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Patient Name', 'Doctor Name', 'Specialization', 'Date', 'Time', 'Status', 'Reason']);

        foreach ($rows as $row) {
            fputcsv($out, [
                $row['patient_name'],
                $row['doctor_name'],
                $row['specialization_name'],
                $row['appt_date'],
                $row['appt_time'],
                $row['status'],
                $row['reason']
            ]);
        }

        fclose($out);
        exit;
    }
}
