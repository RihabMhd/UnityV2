<?php

namespace Interfaces;
use Models\Patient;

interface PatientInterface
{
    public function findById(int $id): ?Patient;

    public function findByUserId(int $userId): ?Patient;

    public function findAll(): array;

    public function findByDoctor(int $doctorId): array;

    public function create(Patient $patient): bool;

    public function update(Patient $patient): bool;

    public function delete(int $id): bool;

    public function findWithUserDetails(int $patientId): ?array;

    public function searchByName(string $name): array;

    public function findByAgeRange(int $minAge, int $maxAge): array;
}
