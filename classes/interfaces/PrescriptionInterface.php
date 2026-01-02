<?php

namespace Interfaces;
use Models\Prescription;

interface PrescriptionInterface
{
    public function findById(int $id): ?Prescription;

    public function findAll(): array;

    public function findByPatient(int $patientId): array;

    public function findByDoctor(int $doctorId): array;

    public function findByMedication(int $medicationId): array;

    public function findByDate(string $date): array;

    public function findByDateRange(string $startDate, string $endDate): array;

    public function create(Prescription $prescription): bool;

    public function update(Prescription $prescription): bool;

    public function delete(int $id): bool;

    public function findWithDetails(int $prescriptionId): ?array;

    public function getRecentForPatient(int $patientId, int $limit = 10): array;
}
