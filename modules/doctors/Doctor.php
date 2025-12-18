<?php

namespace Models;

class Doctor
{
    private $conn;

    private $table_name = "doctors";

    public $doctor_id;
    public $first_name;
    public $last_name;
    public $specialization;
    public $phone_number;
    public $email;
    public $department_id;


    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function readAllDoctors()
    {
        $query = "SELECT d.*, dep.department_name, dep.location FROM " . $this->table_name .
            " d left join departments dep on dep.department_id=d.department_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function readOneDoctors()
    {
        $query = "SELECT d.*, dep.department_name, dep.location FROM " . $this->table_name .
            " d left join departments dep on dep.department_id=d.department_id WHERE doctor_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->doctor_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function createDoctor()
    {
        $query = "INSERT INTO " . $this->table_name . " (first_name, last_name, specialization, phone_number, email,department_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            "sssssi",
            $this->first_name,
            $this->last_name,
            $this->specialization,
            $this->phone_number,
            $this->email,
            $this->department_id
        );
        return $stmt->execute();
    }

    public function updateDoctor()
    {
        $query = "UPDATE " . $this->table_name . " SET first_name=?, last_name=?, specialization=?, phone_number=?, email=?, department_id=? WHERE doctor_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            "sssssii",
            $this->first_name,
            $this->last_name,
            $this->specialization,
            $this->phone_number,
            $this->email,
            $this->department_id,
            $this->doctor_id
        );
        return $stmt->execute();
    }

    public function deleteDoctor()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE doctor_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            "i",
            $this->doctor_id
        );
        return $stmt->execute();
    }
}
