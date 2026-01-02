<?php

namespace Models;

use InvalidArgumentException;

class Prescription
{
    private ?int $prescription_id = null;
    private int $patient_id;
    private int $doctor_id;
    private int $medication_id;
    private string $prescription_date;
    private string $dosage_instructions;

    public function __construct()
    {
        $this->prescription_date = date('Y-m-d');
    }

    public function getPrescriptionId(): ?int
    {
        return $this->prescription_id;
    }

    public function getPatientId(): int
    {
        return $this->patient_id;
    }

    public function getDoctorId(): int
    {
        return $this->doctor_id;
    }

    public function getMedicationId(): int
    {
        return $this->medication_id;
    }

    public function getPrescriptionDate(): string
    {
        return $this->prescription_date;
    }

    public function getDosageInstructions(): string
    {
        return $this->dosage_instructions;
    }

    public function setPrescriptionId(int $prescription_id): void
    {
        if ($prescription_id <= 0) {
            throw new InvalidArgumentException("ID invalide.");
        }
        $this->prescription_id = $prescription_id;
    }

    public function setPatientId(int $patient_id): void
    {
        if ($patient_id <= 0) {
            throw new InvalidArgumentException("Patient ID invalide.");
        }
        $this->patient_id = $patient_id;
    }

    public function setDoctorId(int $doctor_id): void
    {
        if ($doctor_id <= 0) {
            throw new InvalidArgumentException("Doctor ID invalide.");
        }
        $this->doctor_id = $doctor_id;
    }

    public function setMedicationId(int $medication_id): void
    {
        if ($medication_id <= 0) {
            throw new InvalidArgumentException("Medication ID invalide.");
        }
        $this->medication_id = $medication_id;
    }

    public function setPrescriptionDate(string $prescription_date): void
    {
        if (!$this->isValidDate($prescription_date)) {
            throw new InvalidArgumentException("Format de date invalide (YYYY-MM-DD attendu).");
        }
        $this->prescription_date = $prescription_date;
    }

    public function setDosageInstructions(string $dosage_instructions): void
    {
        if (empty(trim($dosage_instructions))) {
            throw new InvalidArgumentException("Les instructions de dosage sont obligatoires.");
        }
        $this->dosage_instructions = trim($dosage_instructions);
    }

    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    public function __toString(): string
    {
        return sprintf(
            "Prescription #%d | Patient: %d | Doctor: %d | Medication: %d | Date: %s",
            $this->prescription_id ?? 0,
            $this->patient_id ?? 0,
            $this->doctor_id ?? 0,
            $this->medication_id ?? 0,
            $this->prescription_date ?? 'N/A'
        );
    }
}