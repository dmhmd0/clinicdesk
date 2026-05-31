<?php

require_once __DIR__ . '/BaseModel.php';

class SpecializationModel extends BaseModel
{
    public function getAll()
    {
        $result = $this->execute("SELECT * FROM specializations ORDER BY name ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function findById($id)
    {
        $result = $this->execute("SELECT * FROM specializations WHERE id = ?", "i", [$id]);
        return $result ? $result->fetch_assoc() : null;
    }

    public function create($name)
    {
        $this->execute(
            "INSERT INTO specializations (name) VALUES (?)",
            "s",
            [$name]
        );

        return $this->db->lastInsertId();
    }

    public function update($id, $name)
    {
        return $this->execute(
            "UPDATE specializations SET name = ? WHERE id = ?",
            "si",
            [$name, $id]
        );
    }

    public function delete($id)
    {
        return $this->execute(
            "DELETE FROM specializations WHERE id = ?",
            "i",
            [$id]
        );
    }

    public function isSafeToDelete($id)
    {
        $result = $this->execute(
            "SELECT COUNT(*) AS total FROM doctors WHERE specialization_id = ?",
            "i",
            [$id]
        );

        $row = $result->fetch_assoc();
        return (int)$row['total'] === 0;
    }
}