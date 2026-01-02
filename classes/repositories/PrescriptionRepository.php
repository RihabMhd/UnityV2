<?php

namespace Repositories;

use Models\Prescription;
use Interfaces\PrescriptionInterface;
use PDO;
use PDOException;

class PrescriptionRepository implements PrescriptionInterface
{
    private $conn;
    private string $table_name = "prescriptions";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findById(int $id): ?Prescription
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE prescription_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->mapToEntity($row);
        }

        return null;
    }

    public function findAll(): array
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY prescription_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $prescriptions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $prescriptions[] = $this->mapToEntity($row);
        }

        return $prescriptions;
    }

    public function findByPatient(int $patientId): array
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE patient_id = :patientId 
                  ORDER BY prescription_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patientId', $patientId);
        $stmt->execute();

        $prescriptions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $prescriptions[] = $this->mapToEntity($row);
        }

        return $prescriptions;
    }

    public function findByDoctor(int $doctorId): array
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE doctor_id = :doctorId 
                  ORDER BY prescription_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctorId', $doctorId);
        $stmt->execute();

        $prescriptions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $prescriptions[] = $this->mapToEntity($row);
        }

        return $prescriptions;
    }

    public function findByMedication(int $medicationId): array
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE medication_id = :medicationId 
                  ORDER BY prescription_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':medicationId', $medicationId);
        $stmt->execute();

        $prescriptions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $prescriptions[] = $this->mapToEntity($row);
        }

        return $prescriptions;
    }

    public function findByDate(string $date): array
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE prescription_date = :date 
                  ORDER BY prescription_id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();

        $prescriptions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $prescriptions[] = $this->mapToEntity($row);
        }

        return $prescriptions;
    }

    public function findByDateRange(string $startDate, string $endDate): array
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE prescription_date BETWEEN :startDate AND :endDate 
                  ORDER BY prescription_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();

        $prescriptions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $prescriptions[] = $this->mapToEntity($row);
        }

        return $prescriptions;
    }

    public function create(Prescription $prescription): bool
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  (patient_id, doctor_id, medication_id, prescription_date, dosage_instructions) 
                  VALUES (:patient_id, :doctor_id, :medication_id, :prescription_date, :dosage_instructions)";

        $stmt = $this->conn->prepare($query);

        $patientId = $prescription->getPatientId();
        $doctorId = $prescription->getDoctorId();
        $medicationId = $prescription->getMedicationId();
        $prescriptionDate = $prescription->getPrescriptionDate();
        $dosageInstructions = $prescription->getDosageInstructions();

        $stmt->bindParam(':patient_id', $patientId);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->bindParam(':medication_id', $medicationId);
        $stmt->bindParam(':prescription_date', $prescriptionDate);
        $stmt->bindParam(':dosage_instructions', $dosageInstructions);

        if ($stmt->execute()) {
            $prescription->setPrescriptionId((int)$this->conn->lastInsertId());
            return true;
        }

        return false;
    }

    public function update(Prescription $prescription): bool
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET patient_id = :patient_id, 
                      doctor_id = :doctor_id, 
                      medication_id = :medication_id, 
                      prescription_date = :prescription_date, 
                      dosage_instructions = :dosage_instructions 
                  WHERE prescription_id = :prescription_id";

        $stmt = $this->conn->prepare($query);

        $prescriptionId = $prescription->getPrescriptionId();
        $patientId = $prescription->getPatientId();
        $doctorId = $prescription->getDoctorId();
        $medicationId = $prescription->getMedicationId();
        $prescriptionDate = $prescription->getPrescriptionDate();
        $dosageInstructions = $prescription->getDosageInstructions();

        $stmt->bindParam(':prescription_id', $prescriptionId);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->bindParam(':medication_id', $medicationId);
        $stmt->bindParam(':prescription_date', $prescriptionDate);
        $stmt->bindParam(':dosage_instructions', $dosageInstructions);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE prescription_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function findWithDetails(int $prescriptionId): ?array
    {
        $query = "SELECT p.*, 
                         CONCAT(pat.first_name, ' ', pat.last_name) as patient_name,
                         pat.phone_number as patient_phone,
                         CONCAT(d.first_name, ' ', d.last_name) as doctor_name,
                         d.specialization as doctor_specialization,
                         m.medication_name,
                         m.dosage as medication_dosage
                  FROM " . $this->table_name . " p
                  LEFT JOIN patients pat ON p.patient_id = pat.patient_id
                  LEFT JOIN doctors d ON p.doctor_id = d.doctor_id
                  LEFT JOIN medications m ON p.medication_id = m.medication_id
                  WHERE p.prescription_id = :prescriptionId LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':prescriptionId', $prescriptionId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getRecentForPatient(int $patientId, int $limit = 10): array
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE patient_id = :patientId 
                  ORDER BY prescription_date DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patientId', $patientId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $prescriptions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $prescriptions[] = $this->mapToEntity($row);
        }

        return $prescriptions;
    }

    private function mapToEntity(array $row): Prescription
    {
        $prescription = new Prescription($this->conn);
        $prescription->setPrescriptionId((int)$row['prescription_id']);
        $prescription->setPatientId((int)$row['patient_id']);
        $prescription->setDoctorId((int)$row['doctor_id']);
        $prescription->setMedicationId((int)$row['medication_id']);
        $prescription->setPrescriptionDate($row['prescription_date']);
        $prescription->setDosageInstructions($row['dosage_instructions']);

        return $prescription;
    }
}