<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../config/config.php';

class AppointmentModel extends BaseModel
{
    public function book($data)
    {
        if ($this->hasConflict($data['doctor_id'], $data['appt_date'], $data['appt_time'])) {
            return false;
        }

        return $this->execute(
            "INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, reason, status)
             VALUES (?, ?, ?, ?, ?, 'pending')",
            "iisss",
            [
                $data['patient_id'],
                $data['doctor_id'],
                $data['appt_date'],
                $data['appt_time'],
                $data['reason']
            ]
        );
    }

    public function hasConflict($doctorId, $date, $time)
    {
        $result = $this->execute(
            "SELECT id FROM appointments
             WHERE doctor_id = ? AND appt_date = ? AND appt_time = ?
             AND status != 'cancelled'
             LIMIT 1",
            "iss",
            [$doctorId, $date, $time]
        );

        return $result && $result->num_rows > 0;
    }

    public function findById($id)
    {
        $result = $this->execute(
            "SELECT appointments.*,
                    patient.name AS patient_name,
                    patient.email AS patient_email,
                    doctor_user.name AS doctor_name,
                    doctors.user_id AS doctor_user_id,
                    specializations.name AS specialization_name,
                    prescriptions.id AS prescription_id,
                    prescriptions.file_path
             FROM appointments
             JOIN users AS patient ON appointments.patient_id = patient.id
             JOIN doctors ON appointments.doctor_id = doctors.id
             JOIN users AS doctor_user ON doctors.user_id = doctor_user.id
             JOIN specializations ON doctors.specialization_id = specializations.id
             LEFT JOIN prescriptions ON prescriptions.appointment_id = appointments.id
             WHERE appointments.id = ?",
            "i",
            [$id]
        );

        return $result ? $result->fetch_assoc() : null;
    }

    public function getByPatient($patientId, $page, $filters = [])
    {
        $offset = ((int)$page - 1) * ITEMS_PER_PAGE;

        $conditions = ["appointments.patient_id = ?"];
        $types = "i";
        $params = [$patientId];

        $this->applyFilters($conditions, $types, $params, $filters);

        $where = "WHERE " . implode(" AND ", $conditions);

        $sql = "SELECT appointments.*,
                       users.name AS doctor_name,
                       specializations.name AS specialization_name,
                       prescriptions.id AS prescription_id
                FROM appointments
                JOIN doctors ON appointments.doctor_id = doctors.id
                JOIN users ON doctors.user_id = users.id
                JOIN specializations ON doctors.specialization_id = specializations.id
                LEFT JOIN prescriptions ON prescriptions.appointment_id = appointments.id
                $where
                ORDER BY appointments.appt_date DESC, appointments.appt_time DESC
                LIMIT ? OFFSET ?";

        $types .= "ii";
        $params[] = ITEMS_PER_PAGE;
        $params[] = $offset;

        $result = $this->execute($sql, $types, $params);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getByDoctor($doctorId, $page, $filters = [])
    {
        $offset = ((int)$page - 1) * ITEMS_PER_PAGE;

        $conditions = ["appointments.doctor_id = ?"];
        $types = "i";
        $params = [$doctorId];

        $this->applyFilters($conditions, $types, $params, $filters);

        $where = "WHERE " . implode(" AND ", $conditions);

        $sql = "SELECT appointments.*,
                       users.name AS patient_name,
                       users.email AS patient_email,
                       prescriptions.id AS prescription_id
                FROM appointments
                JOIN users ON appointments.patient_id = users.id
                LEFT JOIN prescriptions ON prescriptions.appointment_id = appointments.id
                $where
                ORDER BY appointments.appt_date ASC, appointments.appt_time ASC
                LIMIT ? OFFSET ?";

        $types .= "ii";
        $params[] = ITEMS_PER_PAGE;
        $params[] = $offset;

        $result = $this->execute($sql, $types, $params);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAll($page, $filters = [])
    {
        $offset = ((int)$page - 1) * ITEMS_PER_PAGE;

        $conditions = [];
        $types = "";
        $params = [];

        $this->applyFilters($conditions, $types, $params, $filters);

        if (!empty($filters['doctor_id'])) {
            $conditions[] = "appointments.doctor_id = ?";
            $types .= "i";
            $params[] = (int)$filters['doctor_id'];
        }

        if (!empty($filters['patient_name'])) {
            $conditions[] = "patient.name LIKE ?";
            $types .= "s";
            $params[] = "%" . $filters['patient_name'] . "%";
        }

        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        $sql = "SELECT appointments.*,
                       patient.name AS patient_name,
                       doctor_user.name AS doctor_name,
                       specializations.name AS specialization_name,
                       prescriptions.id AS prescription_id
                FROM appointments
                JOIN users AS patient ON appointments.patient_id = patient.id
                JOIN doctors ON appointments.doctor_id = doctors.id
                JOIN users AS doctor_user ON doctors.user_id = doctor_user.id
                JOIN specializations ON doctors.specialization_id = specializations.id
                LEFT JOIN prescriptions ON prescriptions.appointment_id = appointments.id
                $where
                ORDER BY appointments.appt_date DESC, appointments.appt_time DESC
                LIMIT ? OFFSET ?";

        $types .= "ii";
        $params[] = ITEMS_PER_PAGE;
        $params[] = $offset;

        $result = $this->execute($sql, $types, $params);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countFiltered($scope = "all", $scopeId = 0, $filters = [])
    {
        $conditions = [];
        $types = "";
        $params = [];

        if ($scope === "patient") {
            $conditions[] = "appointments.patient_id = ?";
            $types .= "i";
            $params[] = $scopeId;
        }

        if ($scope === "doctor") {
            $conditions[] = "appointments.doctor_id = ?";
            $types .= "i";
            $params[] = $scopeId;
        }

        $this->applyFilters($conditions, $types, $params, $filters);

        if (!empty($filters['doctor_id'])) {
            $conditions[] = "appointments.doctor_id = ?";
            $types .= "i";
            $params[] = (int)$filters['doctor_id'];
        }

        if (!empty($filters['patient_name'])) {
            $conditions[] = "patient.name LIKE ?";
            $types .= "s";
            $params[] = "%" . $filters['patient_name'] . "%";
        }

        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        $sql = "SELECT COUNT(*) AS total
                FROM appointments
                JOIN users AS patient ON appointments.patient_id = patient.id
                $where";

        $result = $this->execute($sql, $types, $params);
        $row = $result ? $result->fetch_assoc() : ['total' => 0];

        return (int)$row['total'];
    }

    public function updateStatus($id, $status, $notes = "")
    {
        return $this->execute(
            "UPDATE appointments SET status = ?, doctor_notes = ? WHERE id = ?",
            "ssi",
            [$status, $notes, $id]
        );
    }

    public function getTodayByDoctor($doctorId)
    {
        $result = $this->execute(
            "SELECT appointments.*, users.name AS patient_name
             FROM appointments
             JOIN users ON appointments.patient_id = users.id
             WHERE appointments.doctor_id = ?
             AND appointments.appt_date = CURDATE()
             ORDER BY appointments.appt_time ASC",
            "i",
            [$doctorId]
        );

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getRecent($limit = 5)
    {
        $result = $this->execute(
            "SELECT appointments.*,
                    patient.name AS patient_name,
                    doctor_user.name AS doctor_name
             FROM appointments
             JOIN users AS patient ON appointments.patient_id = patient.id
             JOIN doctors ON appointments.doctor_id = doctors.id
             JOIN users AS doctor_user ON doctors.user_id = doctor_user.id
             ORDER BY appointments.created_at DESC
             LIMIT ?",
            "i",
            [$limit]
        );

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getReportRows($filters)
    {
        $conditions = ["appointments.appt_date BETWEEN ? AND ?"];
        $types = "ss";
        $params = [$filters['start_date'], $filters['end_date']];

        if (!empty($filters['doctor_id'])) {
            $conditions[] = "appointments.doctor_id = ?";
            $types .= "i";
            $params[] = (int)$filters['doctor_id'];
        }

        if (!empty($filters['status'])) {
            $conditions[] = "appointments.status = ?";
            $types .= "s";
            $params[] = $filters['status'];
        }

        $where = "WHERE " . implode(" AND ", $conditions);

        $sql = "SELECT patient.name AS patient_name,
                       doctor_user.name AS doctor_name,
                       specializations.name AS specialization_name,
                       appointments.appt_date,
                       appointments.appt_time,
                       appointments.status,
                       appointments.reason
                FROM appointments
                JOIN users AS patient ON appointments.patient_id = patient.id
                JOIN doctors ON appointments.doctor_id = doctors.id
                JOIN users AS doctor_user ON doctors.user_id = doctor_user.id
                JOIN specializations ON doctors.specialization_id = specializations.id
                $where
                ORDER BY appointments.appt_date ASC, appointments.appt_time ASC";

        $result = $this->execute($sql, $types, $params);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    private function applyFilters(&$conditions, &$types, &$params, $filters)
    {
        if (!empty($filters['status'])) {
            $conditions[] = "appointments.status = ?";
            $types .= "s";
            $params[] = $filters['status'];
        }

        if (!empty($filters['start_date'])) {
            $conditions[] = "appointments.appt_date >= ?";
            $types .= "s";
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $conditions[] = "appointments.appt_date <= ?";
            $types .= "s";
            $params[] = $filters['end_date'];
        }
    }
}
