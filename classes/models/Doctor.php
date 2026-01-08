<?php

namespace Models;

use InvalidArgumentException;

class Doctor extends User
{
    private ?int $doctor_id = null;
    private string $first_name;
    private string $last_name;
    private string $specialization;
    private string $phone_number;
    private ?int $department_id = null;
    private ?string $department_name = null;

    public function __construct()
    {
        $this->role = self::ROLE_DOCTOR;
    }

    public function getDoctorId(): ?int
    {
        return $this->doctor_id;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function getSpecialization(): string
    {
        return $this->specialization;
    }

    public function getPhoneNumber(): string
    {
        return $this->phone_number;
    }

    public function getDepartmentId(): ?int
    {
        return $this->department_id;
    }
    public function getDepartmentName(): ?string
    {
        return $this->department_name;
    }

    public function setDoctorId(int $doctor_id): void
    {
        if ($doctor_id <= 0) {
            throw new InvalidArgumentException("ID invalide.");
        }
        $this->doctor_id = $doctor_id;
    }

    public function setFirstName(string $first_name): void
    {
        if (empty(trim($first_name))) {
            throw new InvalidArgumentException("Le prénom est obligatoire.");
        }
        $this->first_name = trim($first_name);
    }
    public function setDepartmentName(string $department_name): void
    {
        if (empty(trim($department_name))) {
            throw new InvalidArgumentException("La department est obligatoire.");
        }
        $this->department_name = trim($department_name);
    }

    public function setLastName(string $last_name): void
    {
        if (empty(trim($last_name))) {
            throw new InvalidArgumentException("Le nom est obligatoire.");
        }
        $this->last_name = trim($last_name);
    }

    public function setSpecialization(string $specialization): void
    {
        if (empty(trim($specialization))) {
            throw new InvalidArgumentException("La spécialisation est obligatoire.");
        }
        $this->specialization = trim($specialization);
    }

    public function setPhoneNumber(string $phone_number): void
    {
        if (empty(trim($phone_number))) {
            throw new InvalidArgumentException("Le numéro de téléphone est obligatoire.");
        }
        $this->phone_number = trim($phone_number);
    }

    public function setDepartmentId(?int $department_id): void
    {
        if ($department_id !== null && $department_id <= 0) {
            throw new InvalidArgumentException("Department ID invalide.");
        }
        $this->department_id = $department_id;
    }

    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function __toString(): string
    {
        return sprintf(
            "Doctor #%d | Name: %s %s | Email: %s | Specialization: %s | Department: %s",
            $this->doctor_id ?? 0,
            $this->first_name ?? 'N/A',
            $this->last_name ?? 'N/A',
            $this->email ?? 'N/A',
            $this->specialization ?? 'N/A',
            $this->department_id ?? 'N/A'
        );
    }
}