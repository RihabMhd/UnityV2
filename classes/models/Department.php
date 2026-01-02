<?php

namespace Models;

use InvalidArgumentException;

class Department
{
    private ?int $department_id = null;
    private string $department_name;
    private string $location;

    public function getDepartmentId(): ?int
    {
        return $this->department_id;
    }

    public function getDepartmentName(): string
    {
        return $this->department_name;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setDepartmentId(int $department_id): void
    {
        if ($department_id <= 0) {
            throw new InvalidArgumentException("ID invalide.");
        }
        $this->department_id = $department_id;
    }

    public function setDepartmentName(string $department_name): void
    {
        if (empty(trim($department_name))) {
            throw new InvalidArgumentException("Le nom du département est obligatoire.");
        }
        $this->department_name = trim($department_name);
    }

    public function setLocation(string $location): void
    {
        if (empty(trim($location))) {
            throw new InvalidArgumentException("La localisation du département est obligatoire.");
        }
        $this->location = trim($location);
    }

    public function __toString(): string
    {
        return sprintf(
            "Department #%d | Name: %s | Location: %s",
            $this->department_id ?? 0,
            $this->department_name ?? 'N/A',
            $this->location ?? 'N/A'
        );
    }
}