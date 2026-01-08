<?php

namespace Repositories;

use Models\Patient;
use Interfaces\PatientInterface;
use PDO;
use PDOException;

class PatientRepository implements PatientInterface
{
    private $conn;
    private string $table_name = "patients";
    private string $users_table = "users";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findById(int $id): ?Patient
    {
        $query = "SELECT p.*, u.* 
                  FROM " . $this->table_name . " p
                  INNER JOIN " . $this->users_table . " u ON p.patient_id = u.id
                  WHERE p.patient_id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->mapToEntity($row);
        }

        return null;
    }

    public function findByUserId(int $userId): ?Patient
    {
        $query = "SELECT p.*, u.* 
                  FROM " . $this->table_name . " p
                  INNER JOIN " . $this->users_table . " u ON p.patient_id = u.id
                  WHERE p.patient_id = :userId LIMIT 1";
        
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
        $query = "SELECT p.*, u.* 
                  FROM " . $this->table_name . " p
                  INNER JOIN " . $this->users_table . " u ON p.patient_id = u.id
                  ORDER BY p.last_name, p.first_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $patients = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $patients[] = $this->mapToEntity($row);
        }

        return $patients;
    }

    public function findByDoctor(int $doctorId): array
    {
        $query = "SELECT p.*, u.* 
                  FROM " . $this->table_name . " p
                  INNER JOIN " . $this->users_table . " u ON p.patient_id = u.id
                  WHERE p.doctor_id = :doctorId
                  ORDER BY p.last_name, p.first_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctorId', $doctorId);
        $stmt->execute();

        $patients = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $patients[] = $this->mapToEntity($row);
        }

        return $patients;
    }

 
    public function create(Patient $patient): bool
    {
        // Check if doctor_id column exists in the table
        $query = "INSERT INTO " . $this->table_name . " 
                  (patient_id, first_name, last_name, date_of_birth, phone_number, address) 
                  VALUES (:patient_id, :first_name, :last_name, :date_of_birth, :phone_number, :address)";

        $stmt = $this->conn->prepare($query);

        $patientId = $patient->getId(); // patient_id = user_id
        $firstName = $patient->getFirstName();
        $lastName = $patient->getLastName();
        $dateOfBirth = $patient->getDateOfBirth();
        $phoneNumber = $patient->getPhoneNumber();
        $address = $patient->getAddress();

        $stmt->bindParam(':patient_id', $patientId);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':date_of_birth', $dateOfBirth);
        $stmt->bindParam(':phone_number', $phoneNumber);
        $stmt->bindParam(':address', $address);

        if ($stmt->execute()) {
            $patient->setPatientId($patientId);
            return true;
        }

        return false;
    }

    public function update(Patient $patient): bool
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET first_name = :first_name, 
                      last_name = :last_name, 
                      date_of_birth = :date_of_birth, 
                      phone_number = :phone_number, 
                      address = :address
                  WHERE patient_id = :patient_id";

        $stmt = $this->conn->prepare($query);

        $patientId = $patient->getPatientId();
        $firstName = $patient->getFirstName();
        $lastName = $patient->getLastName();
        $dateOfBirth = $patient->getDateOfBirth();
        $phoneNumber = $patient->getPhoneNumber();
        $address = $patient->getAddress();

        $stmt->bindParam(':patient_id', $patientId);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':date_of_birth', $dateOfBirth);
        $stmt->bindParam(':phone_number', $phoneNumber);
        $stmt->bindParam(':address', $address);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE patient_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function findWithUserDetails(int $patientId): ?array
    {
        $query = "SELECT p.*, u.username, u.email, u.role, u.created_at,
                         CONCAT(d.first_name, ' ', d.last_name) as doctor_name,
                         d.specialization as doctor_specialization
                  FROM " . $this->table_name . " p
                  INNER JOIN " . $this->users_table . " u ON p.patient_id = u.id
                  LEFT JOIN doctors d ON p.doctor_id = d.doctor_id
                  WHERE p.patient_id = :patientId LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patientId', $patientId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function searchByName(string $name): array
    {
        $query = "SELECT p.*, u.* 
                  FROM " . $this->table_name . " p
                  INNER JOIN " . $this->users_table . " u ON p.patient_id = u.id
                  WHERE CONCAT(p.first_name, ' ', p.last_name) LIKE :name
                  ORDER BY p.last_name, p.first_name";
        
        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $name . '%';
        $stmt->bindParam(':name', $searchTerm);
        $stmt->execute();

        $patients = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $patients[] = $this->mapToEntity($row);
        }

        return $patients;
    }

    public function findByAgeRange(int $minAge, int $maxAge): array
    {
        $minDate = date('Y-m-d', strtotime("-$maxAge years"));
        $maxDate = date('Y-m-d', strtotime("-$minAge years"));

        $query = "SELECT p.*, u.* 
                  FROM " . $this->table_name . " p
                  INNER JOIN " . $this->users_table . " u ON p.patient_id = u.id
                  WHERE p.date_of_birth BETWEEN :minDate AND :maxDate
                  ORDER BY p.date_of_birth DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':minDate', $minDate);
        $stmt->bindParam(':maxDate', $maxDate);
        $stmt->execute();

        $patients = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $patients[] = $this->mapToEntity($row);
        }

        return $patients;
    }

    private function mapToEntity(array $row): Patient
    {
        $patient = new Patient();
        
        // User fields
        if (isset($row['id'])) {
            $patient->setId((int)$row['id']);
        }
        if (isset($row['username'])) {
            $patient->setUsername($row['username']);
        }
        if (isset($row['email'])) {
            $patient->setEmail($row['email']);
        }
        if (isset($row['password'])) {
            $patient->setPasswordRaw($row['password']);
        }
        if (isset($row['created_at'])) {
            $patient->setCreatedAt($row['created_at']);
        }

        // Patient fields
        $patient->setPatientId((int)$row['patient_id']);
        $patient->setFirstName($row['first_name']);
        $patient->setLastName($row['last_name']);
        $patient->setDateOfBirth($row['date_of_birth']);
        $patient->setPhoneNumber($row['phone_number']);
        $patient->setAddress($row['address']);
        
        if (!empty($row['doctor_id'])) {
            $patient->setDoctorId((int)$row['doctor_id']);
        }

        return $patient;
    }
}