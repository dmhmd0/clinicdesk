<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../config/config.php';

class DoctorModel extends BaseModel
{
    public function findByUserId($userId)
    {
        $result = $this->execute(
            "SELECT doctors.*, users.name, users.email, users.phone, specializations.name AS specialization_name
             FROM doctors
             JOIN users ON doctors.user_id = users.id
             JOIN specializations ON doctors.specialization_id = specializations.id
             WHERE doctors.user_id = ?",
            "i",
            [$userId]
        );

        return $result ? $result->fetch_assoc() : null;
    }

    public function findById($id)
    {
        $result = $this->execute(
            "SELECT doctors.*, users.name, users.email, users.phone, specializations.name AS specialization_name
             FROM doctors
             JOIN users ON doctors.user_id = users.id
             JOIN specializations ON doctors.specialization_id = specializations.id
             WHERE doctors.id = ?",
            "i",
            [$id]
        );

        return $result ? $result->fetch_assoc() : null;
    }

    public function getAll()
    {
        $result = $this->execute(
            "SELECT doctors.*, users.name, specializations.name AS specialization_name
             FROM doctors
             JOIN users ON doctors.user_id = users.id
             JOIN specializations ON doctors.specialization_id = specializations.id
             WHERE users.is_active = 1
             ORDER BY users.name ASC"
        );

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAllPaginated($page)
    {
        $offset = ((int)$page - 1) * ITEMS_PER_PAGE;

        $result = $this->execute(
            "SELECT doctors.*, users.name, users.email, users.phone, specializations.name AS specialization_name
             FROM doctors
             JOIN users ON doctors.user_id = users.id
             JOIN specializations ON doctors.specialization_id = specializations.id
             ORDER BY doctors.id DESC
             LIMIT ? OFFSET ?",
            "ii",
            [ITEMS_PER_PAGE, $offset]
        );

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countAll()
    {
        $result = $this->execute("SELECT COUNT(*) AS total FROM doctors");
        $row = $result->fetch_assoc();

        return (int)$row['total'];
    }

    public function create($data)
    {
        $this->execute(
            "INSERT INTO doctors (user_id, specialization_id, bio, consultation_fee, available_days)
             VALUES (?, ?, ?, ?, ?)",
            "iisds",
            [
                $data['user_id'],
                $data['specialization_id'],
                $data['bio'],
                $data['consultation_fee'],
                $data['available_days']
            ]
        );

        return $this->db->lastInsertId();
    }

    public function update($doctorId, $data)
    {
        return $this->execute(
            "UPDATE doctors
             SET specialization_id = ?, bio = ?, consultation_fee = ?, available_days = ?
             WHERE id = ?",
            "isdsi",
            [
                $data['specialization_id'],
                $data['bio'],
                $data['consultation_fee'],
                $data['available_days'],
                $doctorId
            ]
        );
    }

    public function getAvailableDays($doctorId)
    {
        $result = $this->execute(
            "SELECT available_days FROM doctors WHERE id = ?",
            "i",
            [$doctorId]
        );

        $row = $result ? $result->fetch_assoc() : null;

        if (!$row) {
            return [];
        }

        return explode(',', $row['available_days']);
    }
}