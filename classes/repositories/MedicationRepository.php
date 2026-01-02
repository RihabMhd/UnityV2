<?php

namespace Repositories;

use Models\Medication;
use Interfaces\MedicationInterface;
use PDO;
use PDOException;

class MedicationRepository implements MedicationInterface
{
    private $conn;
    private string $table_name = "medications";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findById(int $id): ?Medication
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE medication_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->mapToEntity($row);
        }

        return null;
    }

    public function findByName(string $name): ?Medication
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE medication_name = :name LIMIT 1";
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
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY medication_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $medications = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $medications[] = $this->mapToEntity($row);
        }

        return $medications;
    }

    public function searchByName(string $name): array
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE medication_name LIKE :name 
                  ORDER BY medication_name";
        
        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $name . '%';
        $stmt->bindParam(':name', $searchTerm);
        $stmt->execute();

        $medications = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $medications[] = $this->mapToEntity($row);
        }

        return $medications;
    }

    public function create(Medication $medication): bool
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  (medication_name, dosage) 
                  VALUES (:medication_name, :dosage)";

        $stmt = $this->conn->prepare($query);

        $medicationName = $medication->getMedicationName();
        $dosage = $medication->getDosage();

        $stmt->bindParam(':medication_name', $medicationName);
        $stmt->bindParam(':dosage', $dosage);

        if ($stmt->execute()) {
            $medication->setMedicationId((int)$this->conn->lastInsertId());
            return true;
        }

        return false;
    }

    public function update(Medication $medication): bool
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET medication_name = :medication_name, 
                      dosage = :dosage 
                  WHERE medication_id = :medication_id";

        $stmt = $this->conn->prepare($query);

        $medicationId = $medication->getMedicationId();
        $medicationName = $medication->getMedicationName();
        $dosage = $medication->getDosage();

        $stmt->bindParam(':medication_id', $medicationId);
        $stmt->bindParam(':medication_name', $medicationName);
        $stmt->bindParam(':dosage', $dosage);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE medication_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE medication_name = :name";
        
        if ($excludeId !== null) {
            $query .= " AND medication_id != :excludeId";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        
        if ($excludeId !== null) {
            $stmt->bindParam(':excludeId', $excludeId);
        }

        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function findAllWithPrescriptionCount(): array
    {
        $query = "SELECT m.*, COUNT(p.prescription_id) as prescription_count
                  FROM " . $this->table_name . " m
                  LEFT JOIN prescriptions p ON m.medication_id = p.medication_id
                  GROUP BY m.medication_id
                  ORDER BY m.medication_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function mapToEntity(array $row): Medication
    {
        $medication = new Medication($this->conn);
        $medication->setMedicationId((int)$row['medication_id']);
        $medication->setMedicationName($row['medication_name']);
        $medication->setDosage($row['dosage']);

        return $medication;
    }
}