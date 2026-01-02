<?php

namespace Models;

use InvalidArgumentException;

class Patient extends User
{
    private ?int $patient_id = null;
    private string $first_name;
    private string $last_name;
    private string $gender;
    private string $date_of_birth;
    private string $phone_number;
    private string $address;
    private ?int $doctor_id = null;

    public const GENDER_MALE = 'Male';
    public const GENDER_FEMALE = 'Female';
    public const GENDER_OTHER = 'Other';

    private const ALLOWED_GENDERS = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
        self::GENDER_OTHER
    ];

    public function __construct()
    {
        parent::__construct();
        $this->role = self::ROLE_PATIENT;
    }

    public function getPatientId(): ?int
    {
        return $this->patient_id;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getDateOfBirth(): string
    {
        return $this->date_of_birth;
    }

    public function getPhoneNumber(): string
    {
        return $this->phone_number;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getDoctorId(): ?int
    {
        return $this->doctor_id;
    }

    public function setPatientId(int $patient_id): void
    {
        if ($patient_id <= 0) {
            throw new InvalidArgumentException("ID invalide.");
        }
        $this->patient_id = $patient_id;
    }

    public function setFirstName(string $first_name): void
    {
        if (empty(trim($first_name))) {
            throw new InvalidArgumentException("Le prénom est obligatoire.");
        }
        $this->first_name = trim($first_name);
    }

    public function setLastName(string $last_name): void
    {
        if (empty(trim($last_name))) {
            throw new InvalidArgumentException("Le nom est obligatoire.");
        }
        $this->last_name = trim($last_name);
    }

    public function setGender(string $gender): void
    {
        if (!in_array($gender, self::ALLOWED_GENDERS)) {
            throw new InvalidArgumentException("Genre invalide.");
        }
        $this->gender = $gender;
    }

    public function setDateOfBirth(string $date_of_birth): void
    {
        if (!$this->isValidDate($date_of_birth)) {
            throw new InvalidArgumentException("Format de date invalide (YYYY-MM-DD attendu).");
        }
        $this->date_of_birth = $date_of_birth;
    }

    public function setPhoneNumber(string $phone_number): void
    {
        if (empty(trim($phone_number))) {
            throw new InvalidArgumentException("Le numéro de téléphone est obligatoire.");
        }
        $this->phone_number = trim($phone_number);
    }

    public function setAddress(string $address): void
    {
        if (empty(trim($address))) {
            throw new InvalidArgumentException("L'adresse est obligatoire.");
        }
        $this->address = trim($address);
    }

    public function setDoctorId(?int $doctor_id): void
    {
        if ($doctor_id !== null && $doctor_id <= 0) {
            throw new InvalidArgumentException("Doctor ID invalide.");
        }
        $this->doctor_id = $doctor_id;
    }

    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAge(): int
    {
        $dob = new \DateTime($this->date_of_birth);
        $now = new \DateTime();
        return $now->diff($dob)->y;
    }

    public function __toString(): string
    {
        return sprintf(
            "Patient #%d | Name: %s %s | Email: %s | Gender: %s | DOB: %s | Doctor ID: %s",
            $this->patient_id ?? 0,
            $this->first_name ?? 'N/A',
            $this->last_name ?? 'N/A',
            $this->email ?? 'N/A',
            $this->gender ?? 'N/A',
            $this->date_of_birth ?? 'N/A',
            $this->doctor_id ?? 'N/A'
        );
    }
}