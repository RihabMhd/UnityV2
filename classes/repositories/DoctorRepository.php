<?php

namespace Repositories;

use Models\Doctor;
use Interfaces\DoctorInterface;
use PDO;
use PDOException;

class DoctorRepository implements DoctorInterface
{
    private $conn;
    private string $table_name = "doctors";
    private string $users_table = "users";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findById(int $id): ?Doctor
    {
        $query = "SELECT d.*, u.*, dep.department_name 
              FROM " . $this->table_name . " d
              INNER JOIN " . $this->users_table . " u ON d.doctor_id = u.id
              LEFT JOIN departments dep ON d.department_id = dep.department_id
              WHERE d.doctor_id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->mapToEntity($row);
        }

        return null;
    }

    public function findByUserId(int $userId): ?Doctor
    {
        $query = "SELECT d.*, u.*, dep.department_name 
              FROM " . $this->table_name . " d
              INNER JOIN " . $this->users_table . " u ON d.doctor_id = u.id
              LEFT JOIN departments dep ON d.department_id = dep.department_id
              WHERE d.doctor_id = :userId LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->mapToEntity($row);
        }

        return null;
    }

    public function findAll(): array
    {
        $query = "SELECT d.*, u.*, dep.department_name 
              FROM " . $this->table_name . " d
              INNER JOIN " . $this->users_table . " u ON d.doctor_id = u.id
              LEFT JOIN departments dep ON d.department_id = dep.department_id
              ORDER BY d.last_name, d.first_name";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $doctors = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $doctors[] = $this->mapToEntity($row);
        }

        return $doctors;
    }


    public function findByDepartment(int $departmentId): array
    {
        $query = "SELECT d.*, u.* 
                  FROM " . $this->table_name . " d
                  INNER JOIN " . $this->users_table . " u ON d.doctor_id = u.id
                  WHERE d.department_id = :departmentId
                  ORDER BY d.last_name, d.first_name";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':departmentId', $departmentId);
        $stmt->execute();

        $doctors = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $doctors[] = $this->mapToEntity($row);
        }

        return $doctors;
    }

    public function findBySpecialization(string $specialization): array
    {
        $query = "SELECT d.*, u.* 
                  FROM " . $this->table_name . " d
                  INNER JOIN " . $this->users_table . " u ON d.doctor_id = u.id
                  WHERE d.specialization = :specialization
                  ORDER BY d.last_name, d.first_name";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':specialization', $specialization);
        $stmt->execute();

        $doctors = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $doctors[] = $this->mapToEntity($row);
        }

        return $doctors;
    }

    public function create(Doctor $doctor): bool
    {
        $query = "INSERT INTO doctors 
      (doctor_id, first_name, last_name, specialization, phone_number, department_id) 
      VALUES (:doctor_id, :first_name, :last_name, :specialization, :phone_number, :department_id)";

        $stmt = $this->conn->prepare($query);

        $doctorId = $doctor->getId();  
        $firstName = $doctor->getFirstName();
        $lastName = $doctor->getLastName();
        $specialization = $doctor->getSpecialization();
        $phoneNumber = $doctor->getPhoneNumber();
        $departmentId = $doctor->getDepartmentId();

        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':specialization', $specialization);
        $stmt->bindParam(':phone_number', $phoneNumber);
        $stmt->bindParam(':department_id', $departmentId);

        if ($stmt->execute()) {
            $doctor->setDoctorId($doctorId);
            return true;
        }

        return false;
    }

    public function update(Doctor $doctor): bool
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET first_name = :first_name, 
                      last_name = :last_name, 
                      specialization = :specialization, 
                      phone_number = :phone_number, 
                      department_id = :department_id 
                  WHERE doctor_id = :doctor_id";

        $stmt = $this->conn->prepare($query);

        $doctorId = $doctor->getDoctorId();
        $firstName = $doctor->getFirstName();
        $lastName = $doctor->getLastName();
        $specialization = $doctor->getSpecialization();
        $phoneNumber = $doctor->getPhoneNumber();
        $departmentId = $doctor->getDepartmentId();

        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':specialization', $specialization);
        $stmt->bindParam(':phone_number', $phoneNumber);
        $stmt->bindParam(':department_id', $departmentId);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE doctor_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function findWithUserDetails(int $doctorId): ?array
    {
        $query = "SELECT d.*, u.username, u.email, u.role, u.created_at, dep.department_name, dep.location
                  FROM " . $this->table_name . " d
                  INNER JOIN " . $this->users_table . " u ON d.doctor_id = u.id
                  LEFT JOIN departments dep ON d.department_id = dep.department_id
                  WHERE d.doctor_id = :doctorId LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctorId', $doctorId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function searchByName(string $name): array
    {
        $query = "SELECT d.*, u.* 
                  FROM " . $this->table_name . " d
                  INNER JOIN " . $this->users_table . " u ON d.doctor_id = u.id
                  WHERE CONCAT(d.first_name, ' ', d.last_name) LIKE :name
                  ORDER BY d.last_name, d.first_name";

        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $name . '%';
        $stmt->bindParam(':name', $searchTerm);
        $stmt->execute();

        $doctors = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $doctors[] = $this->mapToEntity($row);
        }

        return $doctors;
    }

    private function mapToEntity(array $row): Doctor
    {
        $doctor = new Doctor();

        // User fields
        if (isset($row['id'])) {
            $doctor->setId((int)$row['id']);
        }
        if (isset($row['username'])) {
            $doctor->setUsername($row['username']);
        }
        if (isset($row['email'])) {
            $doctor->setEmail($row['email']);
        }
        if (isset($row['password'])) {
            $doctor->setPasswordRaw($row['password']);
        }
        if (isset($row['created_at'])) {
            $doctor->setCreatedAt($row['created_at']);
        }

        // Doctor fields
        $doctor->setDoctorId((int)$row['doctor_id']);
        $doctor->setFirstName($row['first_name']);
        $doctor->setLastName($row['last_name']);
        $doctor->setSpecialization($row['specialization']);
        $doctor->setPhoneNumber($row['phone_number']);

        if (!empty($row['department_id'])) {
            $doctor->setDepartmentId((int)$row['department_id']);
        }

        return $doctor;
    }
}
