<?php

namespace Models;

use InvalidArgumentException;

class Medication
{
    private ?int $medication_id = null;
    private string $medication_name;
    private string $dosage;

    public function getMedicationId(): ?int
    {
        return $this->medication_id;
    }

    public function getMedicationName(): string
    {
        return $this->medication_name;
    }

    public function getDosage(): string
    {
        return $this->dosage;
    }

    public function setMedicationId(int $medication_id): void
    {
        if ($medication_id <= 0) {
            throw new InvalidArgumentException("ID invalide.");
        }
        $this->medication_id = $medication_id;
    }

    public function setMedicationName(string $medication_name): void
    {
        if (empty(trim($medication_name))) {
            throw new InvalidArgumentException("Le nom du médicament est obligatoire.");
        }
        $this->medication_name = trim($medication_name);
    }

    public function setDosage(string $dosage): void
    {
        if (empty(trim($dosage))) {
            throw new InvalidArgumentException("Le dosage est obligatoire.");
        }
        $this->dosage = trim($dosage);
    }

    public function __toString(): string
    {
        return sprintf(
            "Medication #%d | Name: %s | Dosage: %s",
            $this->medication_id ?? 0,
            $this->medication_name ?? 'N/A',
            $this->dosage ?? 'N/A'
        );
    }
}