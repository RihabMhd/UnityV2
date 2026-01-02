<?php


namespace Interfaces;
use Models\Doctor;

interface DoctorInterface
{
    public function findById(int $id): ?Doctor;

    public function findByUserId(int $userId): ?Doctor;

    public function findAll(): array;

    public function findByDepartment(int $departmentId): array;

    public function findBySpecialization(string $specialization): array;

    public function create(Doctor $doctor): bool;

    public function update(Doctor $doctor): bool;

    public function delete(int $id): bool;

    public function findWithUserDetails(int $doctorId): ?array;

    public function searchByName(string $name): array;

}
