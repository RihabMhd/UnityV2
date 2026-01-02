<?php

namespace Repositories;

use Models\Appointment;
use Interfaces\AppointmentInterface;
use PDO;
use PDOException;

class AppointmentRepository implements AppointmentInterface
{
    private $conn;
    private string $table_name = "appointments";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findById(int $id): ?Appointment
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE appointment_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->mapToEntity($row);
        }

        return null;
    }

    public function findAll(): array
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY appointment_date DESC, appointment_time DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $appointments = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $appointments[] = $this->mapToEntity($row);
        }

        return $appointments;
    }

    public function findByDoctor(int $doctorId): array
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE doctor_id = :doctorId 
                  ORDER BY appointment_date DESC, appointment_time DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctorId', $doctorId);
        $stmt->execute();

        $appointments = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $appointments[] = $this->mapToEntity($row);
        }

        return $appointments;
    }

    public function findByPatient(int $patientId): array
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE patient_id = :patientId 
                  ORDER BY appointment_date DESC, appointment_time DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patientId', $patientId);
        $stmt->execute();

        $appointments = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $appointments[] = $this->mapToEntity($row);
        }

        return $appointments;
    }

    public function findByDate(string $date): array
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE appointment_date = :date 
                  ORDER BY appointment_time";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();

        $appointments = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $appointments[] = $this->mapToEntity($row);
        }

        return $appointments;
    }

    public function findByDateRange(string $startDate, string $endDate): array
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE appointment_date BETWEEN :startDate AND :endDate 
                  ORDER BY appointment_date, appointment_time";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();

        $appointments = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $appointments[] = $this->mapToEntity($row);
        }

        return $appointments;
    }

    public function create(Appointment $appointment): bool
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  (appointment_date, appointment_time, doctor_id, patient_id, reason) 
                  VALUES (:appointment_date, :appointment_time, :doctor_id, :patient_id, :reason)";

        $stmt = $this->conn->prepare($query);

        $appointmentDate = $appointment->getAppointmentDate();
        $appointmentTime = $appointment->getAppointmentTime();
        $doctorId = $appointment->getDoctorId();
        $patientId = $appointment->getPatientId();
        $reason = $appointment->getReason();

        $stmt->bindParam(':appointment_date', $appointmentDate);
        $stmt->bindParam(':appointment_time', $appointmentTime);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->bindParam(':reason', $reason);

        if ($stmt->execute()) {
            $appointment->setAppointmentId((int)$this->conn->lastInsertId());
            return true;
        }

        return false;
    }

    public function update(Appointment $appointment): bool
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET appointment_date = :appointment_date, 
                      appointment_time = :appointment_time, 
                      doctor_id = :doctor_id, 
                      patient_id = :patient_id, 
                      reason = :reason 
                  WHERE appointment_id = :appointment_id";

        $stmt = $this->conn->prepare($query);

        $appointmentId = $appointment->getAppointmentId();
        $appointmentDate = $appointment->getAppointmentDate();
        $appointmentTime = $appointment->getAppointmentTime();
        $doctorId = $appointment->getDoctorId();
        $patientId = $appointment->getPatientId();
        $reason = $appointment->getReason();

        $stmt->bindParam(':appointment_id', $appointmentId);
        $stmt->bindParam(':appointment_date', $appointmentDate);
        $stmt->bindParam(':appointment_time', $appointmentTime);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->bindParam(':reason', $reason);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE appointment_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function findWithDetails(int $appointmentId): ?array
    {
        $query = "SELECT a.*, 
                         CONCAT(d.first_name, ' ', d.last_name) as doctor_name,
                         d.specialization as doctor_specialization,
                         CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                         p.phone_number as patient_phone
                  FROM " . $this->table_name . " a
                  LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                  LEFT JOIN patients p ON a.patient_id = p.patient_id
                  WHERE a.appointment_id = :appointmentId LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':appointmentId', $appointmentId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function hasConflict(int $doctorId, string $date, string $time, ?int $excludeAppointmentId = null): bool
    {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " 
                  WHERE doctor_id = :doctorId 
                  AND appointment_date = :date 
                  AND appointment_time = :time";
        
        if ($excludeAppointmentId !== null) {
            $query .= " AND appointment_id != :excludeId";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctorId', $doctorId);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        
        if ($excludeAppointmentId !== null) {
            $stmt->bindParam(':excludeId', $excludeAppointmentId);
        }

        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function getUpcomingForDoctor(int $doctorId, int $limit = 10): array
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE doctor_id = :doctorId 
                  AND CONCAT(appointment_date, ' ', appointment_time) >= NOW()
                  ORDER BY appointment_date, appointment_time 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctorId', $doctorId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $appointments = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $appointments[] = $this->mapToEntity($row);
        }

        return $appointments;
    }

    public function getUpcomingForPatient(int $patientId, int $limit = 10): array
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE patient_id = :patientId 
                  AND CONCAT(appointment_date, ' ', appointment_time) >= NOW()
                  ORDER BY appointment_date, appointment_time 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patientId', $patientId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $appointments = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $appointments[] = $this->mapToEntity($row);
        }

        return $appointments;
    }

    private function mapToEntity(array $row): Appointment
    {
        $appointment = new Appointment($this->conn);
        $appointment->setAppointmentId((int)$row['appointment_id']);
        $appointment->setAppointmentDate($row['appointment_date']);
        $appointment->setAppointmentTime($row['appointment_time']);
        $appointment->setDoctorId((int)$row['doctor_id']);
        $appointment->setPatientId((int)$row['patient_id']);
        $appointment->setReason($row['reason']);

        return $appointment;
    }
}