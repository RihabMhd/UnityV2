<?php

namespace Models;

use InvalidArgumentException;

class Medication
{
    private ?int $medication_id = null;
    private string $medication_name;
    private string $dosage;
    private string $code;
    private string $category;
    private string $manufacturer;
    private int $stock_quantity;
    private float $unit_price;
    private string $expiry_date; 
    private string $status;

    
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

    public function getCode(): string
    {
        return $this->code;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }

    public function getStockQuantity(): int
    {
        return $this->stock_quantity;
    }

    public function getUnitPrice(): float
    {
        return $this->unit_price;
    }

    public function getExpiryDate(): string
    {
        return $this->expiry_date;
    }

    public function getStatus(): string
    {
        return $this->status;
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

    public function setCode(string $code): void
    {
        if (empty(trim($code))) {
            throw new InvalidArgumentException("Le code est obligatoire.");
        }
        $this->code = trim($code);
    }

    public function setCategory(string $category): void
    {
        if (empty(trim($category))) {
            throw new InvalidArgumentException("La catégorie est obligatoire.");
        }
        $this->category = trim($category);
    }

    public function setManufacturer(string $manufacturer): void
    {
        if (empty(trim($manufacturer))) {
            throw new InvalidArgumentException("Le fabricant est obligatoire.");
        }
        $this->manufacturer = trim($manufacturer);
    }

    public function setStockQuantity(int $stock_quantity): void
    {
        if ($stock_quantity < 0) {
            throw new InvalidArgumentException("La quantité en stock ne peut pas être négative.");
        }
        $this->stock_quantity = $stock_quantity;
    }

    public function setUnitPrice(float $unit_price): void
    {
        if ($unit_price < 0) {
            throw new InvalidArgumentException("Le prix unitaire ne peut pas être négatif.");
        }
        $this->unit_price = $unit_price;
    }

    public function setExpiryDate(string $expiry_date): void
    {
        // optional: validate YYYY-MM-DD format
        $this->expiry_date = trim($expiry_date);
    }

    public function setStatus(string $status): void
    {
        $valid = ['Available', 'Out of Stock', 'Expired'];
        if (!in_array($status, $valid)) {
            throw new InvalidArgumentException("Statut invalide.");
        }
        $this->status = $status;
    }

   
    public function __toString(): string
    {
        return sprintf(
            "Medication #%d | Name: %s | Dosage: %s | Code: %s | Category: %s | Manufacturer: %s | Stock: %d | Price: %.2f | Expiry: %s | Status: %s",
            $this->medication_id ?? 0,
            $this->medication_name ?? 'N/A',
            $this->dosage ?? 'N/A',
            $this->code ?? 'N/A',
            $this->category ?? 'N/A',
            $this->manufacturer ?? 'N/A',
            $this->stock_quantity ?? 0,
            $this->unit_price ?? 0.0,
            $this->expiry_date ?? 'N/A',
            $this->status ?? 'N/A'
        );
    }
}
