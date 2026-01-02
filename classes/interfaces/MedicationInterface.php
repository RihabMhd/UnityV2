<?php

namespace Interfaces;
use Models\Medication;

interface MedicationInterface
{
    public function findById(int $id): ?Medication;

    public function findByName(string $name): ?Medication;

    public function findAll(): array;

    public function searchByName(string $name): array;

    public function create(Medication $medication): bool;

    public function update(Medication $medication): bool;

    public function delete(int $id): bool;

    public function nameExists(string $name, ?int $excludeId = null): bool;

    public function findAllWithPrescriptionCount(): array;
}
