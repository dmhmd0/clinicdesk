<?php

require_once __DIR__ . '/BaseModel.php';

class PrescriptionModel extends BaseModel
{
    public function findByAppointmentId($apptId)
    {
        $result = $this->execute(
            "SELECT prescriptions.*,
                    appointments.patient_id,
                    appointments.doctor_id,
                    appointments.status,
                    users.name AS patient_name
             FROM prescriptions
             JOIN appointments ON prescriptions.appointment_id = appointments.id
             JOIN users ON appointments.patient_id = users.id
             WHERE prescriptions.appointment_id = ?",
            "i",
            [$apptId]
        );

        return $result ? $result->fetch_assoc() : null;
    }

    public function create($data)
    {
        $this->execute(
            "INSERT INTO prescriptions (appointment_id, diagnosis, medications, notes, file_path)
             VALUES (?, ?, ?, ?, ?)",
            "issss",
            [
                $data['appointment_id'],
                $data['diagnosis'],
                $data['medications'],
                $data['notes'],
                $data['file_path']
            ]
        );

        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        return $this->execute(
            "UPDATE prescriptions
             SET diagnosis = ?, medications = ?, notes = ?, file_path = ?
             WHERE id = ?",
            "ssssi",
            [
                $data['diagnosis'],
                $data['medications'],
                $data['notes'],
                $data['file_path'],
                $id
            ]
        );
    }

    public function getByPatient($patientId)
    {
        $result = $this->execute(
            "SELECT prescriptions.*,
                    appointments.appt_date,
                    doctor_user.name AS doctor_name
             FROM prescriptions
             JOIN appointments ON prescriptions.appointment_id = appointments.id
             JOIN doctors ON appointments.doctor_id = doctors.id
             JOIN users AS doctor_user ON doctors.user_id = doctor_user.id
             WHERE appointments.patient_id = ?
             ORDER BY prescriptions.created_at DESC",
            "i",
            [$patientId]
        );

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function existsForAppointment($apptId)
    {
        $result = $this->execute(
            "SELECT id FROM prescriptions WHERE appointment_id = ? LIMIT 1",
            "i",
            [$apptId]
        );

        return $result && $result->num_rows > 0;
    }
}