<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../config/config.php';

class UserModel extends BaseModel
{
    public function findById($id)
    {
        $result = $this->execute("SELECT * FROM users WHERE id = ?", "i", [$id]);
        return $result ? $result->fetch_assoc() : null;
    }

    public function findByEmail($email)
    {
        $result = $this->execute("SELECT * FROM users WHERE email = ?", "s", [$email]);
        return $result ? $result->fetch_assoc() : null;
    }

    public function create($data)
    {
        $this->execute(
            "INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)",
            "sssss",
            [$data['name'], $data['email'], $data['password'], $data['role'], $data['phone']]
        );

        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        return $this->execute(
            "UPDATE users SET name = ?, phone = ?, avatar = ? WHERE id = ?",
            "sssi",
            [$data['name'], $data['phone'], $data['avatar'], $id]
        );
    }

    public function updatePassword($id, $newHash)
    {
        return $this->execute(
            "UPDATE users SET password = ? WHERE id = ?",
            "si",
            [$newHash, $id]
        );
    }

    public function getAllPaginated($page, $role = "")
    {
        $offset = ((int)$page - 1) * ITEMS_PER_PAGE;

        if ($role !== "") {
            $result = $this->execute(
                "SELECT * FROM users WHERE role = ? ORDER BY id DESC LIMIT ? OFFSET ?",
                "sii",
                [$role, ITEMS_PER_PAGE, $offset]
            );
        } else {
            $result = $this->execute(
                "SELECT * FROM users ORDER BY id DESC LIMIT ? OFFSET ?",
                "ii",
                [ITEMS_PER_PAGE, $offset]
            );
        }

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countAll($role = "")
    {
        if ($role !== "") {
            $result = $this->execute("SELECT COUNT(*) AS total FROM users WHERE role = ?", "s", [$role]);
        } else {
            $result = $this->execute("SELECT COUNT(*) AS total FROM users");
        }

        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }

    public function toggleActive($id)
    {
        return $this->execute(
            "UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?",
            "i",
            [$id]
        );
    }
}