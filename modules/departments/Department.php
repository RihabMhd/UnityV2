<?php

namespace Models;

class Department
{
    private $conn;

    private $table_name = "departments";

    public $department_id;
    public $department_name;
    public $location;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function readAllDepartment()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function readOneDepartment()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE department_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->department_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function createDepartment()
    {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE department_name = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $this->department_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " (department_name, location) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            "ss",
            $this->department_name,
            $this->location
        );
        return $stmt->execute();
    }

    public function updateDepartment()
    {
        $query = "UPDATE " . $this->table_name . " SET department_name=?, location=? WHERE department_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            "ssi",
            $this->department_name,
            $this->location,
            $this->department_id
        );
        return $stmt->execute();
    }

    public function deleteDepartment()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE department_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            "i",
            $this->department_id
        );
        return $stmt->execute();
    }
}
