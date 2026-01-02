<?php

namespace Interfaces;
use Models\Department;

interface DepartmentInterface
{
    public function findById(int $id): ?Department;

    public function findByName(string $name): ?Department;

    public function findAll(): array;

    public function findByLocation(string $location): array;

    public function create(Department $department): bool;

    public function update(Department $department): bool;

    public function delete(int $id): bool;

    public function findWithDoctorCount(int $departmentId): ?array;

    public function nameExists(string $name, ?int $excludeId = null): bool;
}
