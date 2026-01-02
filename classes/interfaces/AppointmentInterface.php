<?php

namespace Interfaces;
use Models\Appointment;

interface AppointmentInterface
{
    public function findById(int $id): ?Appointment;

    public function findAll(): array;

    public function findByDoctor(int $doctorId): array;

    public function findByPatient(int $patientId): array;

    public function findByDate(string $date): array;

    public function findByDateRange(string $startDate, string $endDate): array;

    public function create(Appointment $appointment): bool;

    public function update(Appointment $appointment): bool;

    public function delete(int $id): bool;

    public function findWithDetails(int $appointmentId): ?array;

    public function hasConflict(int $doctorId, string $date, string $time, ?int $excludeAppointmentId = null): bool;

    public function getUpcomingForDoctor(int $doctorId, int $limit = 10): array;

    public function getUpcomingForPatient(int $patientId, int $limit = 10): array;
}