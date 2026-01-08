<?php

namespace Models;

use InvalidArgumentException;

class Appointment
{
    private ?int $appointment_id = null;
    private string $appointment_date;
    private string $appointment_time;
    private int $doctor_id;
    private int $patient_id;
    private string $reason;
    private string $status = 'Pending';
    private ?string $notes = null;
    private ?string $patient_name = null;
    private ?string $doctor_name = null;

    public function getPatientName(): ?string
    {
        return $this->patient_name;
    }
    
    public function setPatientName(string $name): void
    {
        $this->patient_name = $name;
    }

    public function getDoctorName(): ?string
    {
        return $this->doctor_name;
    }
    
    public function setDoctorName(string $name): void
    {
        $this->doctor_name = $name;
    }

    public function getAppointmentId(): ?int
    {
        return $this->appointment_id;
    }

    public function getAppointmentDate(): string
    {
        return $this->appointment_date;
    }

    public function getAppointmentTime(): string
    {
        return $this->appointment_time;
    }

    public function getDoctorId(): int
    {
        return $this->doctor_id;
    }

    public function getPatientId(): int
    {
        return $this->patient_id;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setAppointmentId(int $appointment_id): void
    {
        if ($appointment_id <= 0) {
            throw new InvalidArgumentException("ID invalide.");
        }
        $this->appointment_id = $appointment_id;
    }

    public function setAppointmentDate(string $appointment_date): void
    {
        if (!$this->isValidDate($appointment_date)) {
            throw new InvalidArgumentException("Format de date invalide (YYYY-MM-DD attendu).");
        }
        $this->appointment_date = $appointment_date;
    }

    public function setAppointmentTime(string $appointment_time): void
    {
        if (!$this->isValidTime($appointment_time)) {
            throw new InvalidArgumentException("Format d'heure invalide (HH:MM:SS attendu).");
        }
        $this->appointment_time = $appointment_time;
    }

    public function setDoctorId(int $doctor_id): void
    {
        if ($doctor_id <= 0) {
            throw new InvalidArgumentException("Doctor ID invalide.");
        }
        $this->doctor_id = $doctor_id;
    }

    public function setPatientId(int $patient_id): void
    {
        if ($patient_id <= 0) {
            throw new InvalidArgumentException("Patient ID invalide.");
        }
        $this->patient_id = $patient_id;
    }

    public function setReason(string $reason): void
    {
        if (empty(trim($reason))) {
            throw new InvalidArgumentException("La raison du rendez-vous est obligatoire.");
        }
        $this->reason = trim($reason);
    }

    public function setStatus(string $status): void
    {
        $validStatuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];
        if (!in_array($status, $validStatuses)) {
            throw new InvalidArgumentException("Statut invalide.");
        }
        $this->status = $status;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes ? trim($notes) : null;
    }

    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    private function isValidTime(string $time): bool
    {
        $t = \DateTime::createFromFormat('H:i:s', $time);
        if (!$t) {
            $t = \DateTime::createFromFormat('H:i', $time);
        }
        return $t !== false;
    }

    public function getDateTime(): \DateTime
    {
        return new \DateTime($this->appointment_date . ' ' . $this->appointment_time);
    }

    public function isPast(): bool
    {
        return $this->getDateTime() < new \DateTime();
    }

    public function isFuture(): bool
    {
        return $this->getDateTime() > new \DateTime();
    }

    public function __toString(): string
    {
        return sprintf(
            "Appointment #%d | Date: %s %s | Doctor: %d | Patient: %d | Status: %s | Reason: %s",
            $this->appointment_id ?? 0,
            $this->appointment_date ?? 'N/A',
            $this->appointment_time ?? 'N/A',
            $this->doctor_id ?? 0,
            $this->patient_id ?? 0,
            $this->status ?? 'N/A',
            $this->reason ?? 'N/A'
        );
    }
}