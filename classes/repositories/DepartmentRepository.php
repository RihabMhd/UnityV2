<?php

namespace Repositories;

use Models\Department;
use Interfaces\DepartmentInterface;
use PDO;
use PDOException;

class DepartmentRepository implements DepartmentInterface
{
    private $conn;
    private string $table_name = "departments";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findById(int $id): ?Department
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE department_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->mapToEntity($row);
        }

        return null;
    }

    public function findByName(string $name): ?Department
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE department_name = :name LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->mapToEntity($row);
        }

        return null;
    }

    public function findAll(): array
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY department_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $departments = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $departments[] = $this->mapToEntity($row);
        }

        return $departments;
    }

    public function findByLocation(string $location): array
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE location = :location ORDER BY department_name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':location', $location);
        $stmt->execute();

        $departments = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $departments[] = $this->mapToEntity($row);
        }

        return $departments;
    }

    public function create(Department $department): bool
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  (department_name, description, contact_number, email, location) 
                  VALUES (:department_name, :description, :contact_number, :email, :location)";

        $stmt = $this->conn->prepare($query);

        $departmentName = $department->getDepartmentName();
        $description = $department->getDescription();
        $contactNumber = $department->getContactNumber();
        $email = $department->getEmail();
        $location = $department->getLocation();

        $stmt->bindParam(':department_name', $departmentName);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':contact_number', $contactNumber);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':location', $location);

        if ($stmt->execute()) {
            $department->setDepartmentId((int)$this->conn->lastInsertId());
            return true;
        }

        return false;
    }

    public function update(Department $department): bool
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET department_name = :department_name,
                      description = :description,
                      contact_number = :contact_number,
                      email = :email,
                      location = :location 
                  WHERE department_id = :department_id";

        $stmt = $this->conn->prepare($query);

        $departmentId = $department->getDepartmentId();
        $departmentName = $department->getDepartmentName();
        $description = $department->getDescription();
        $contactNumber = $department->getContactNumber();
        $email = $department->getEmail();
        $location = $department->getLocation();

        $stmt->bindParam(':department_id', $departmentId);
        $stmt->bindParam(':department_name', $departmentName);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':contact_number', $contactNumber);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':location', $location);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE department_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function findWithDoctorCount(int $departmentId): ?array
    {
        $query = "SELECT d.*, COUNT(doc.doctor_id) as doctor_count
                  FROM " . $this->table_name . " d
                  LEFT JOIN doctors doc ON d.department_id = doc.department_id
                  WHERE d.department_id = :departmentId
                  GROUP BY d.department_id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':departmentId', $departmentId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE department_name = :name";
        
        if ($excludeId !== null) {
            $query .= " AND department_id != :excludeId";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        
        if ($excludeId !== null) {
            $stmt->bindParam(':excludeId', $excludeId);
        }

        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    private function mapToEntity(array $row): Department
    {
        $department = new Department();
        $department->setDepartmentId((int)$row['department_id']);
        $department->setDepartmentName($row['department_name']);
        $department->setDescription($row['description'] ?? null);
        $department->setContactNumber($row['contact_number'] ?? null);
        $department->setEmail($row['email'] ?? null);
        $department->setLocation($row['location']);

        return $department;
    }
}