<?php

require_once __DIR__ . '/../config/database.php';

class Database
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_error) {
            throw new RuntimeException('Database connection failed.');
        }

        $this->conn->set_charset('utf8mb4');
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function query($sql, $types = "", $params = [])
    {
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        if (!empty($types) && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            return false;
        }

        if (
            stripos(trim($sql), 'SELECT') === 0 ||
            stripos(trim($sql), 'SHOW') === 0
        ) {
            return $stmt->get_result();
        }

        return true;
    }

    public function lastInsertId()
    {
        return $this->conn->insert_id;
    }
}