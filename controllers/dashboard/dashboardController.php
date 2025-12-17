<?php

namespace Controllers;

require_once "./modules/patients/Patient.php";
require_once "./modules/doctors/Doctor.php";
require_once "./modules/departments/Department.php";

use Models\Patient;
use Models\Doctor;
use Models\Department;

class DashboardController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function index()
    {
        $stats = $this->getStatistics();

        $recentPatients = $this->getRecentPatients(5);

        $patientsByGender = $this->getPatientsByGender();

        $doctorsByDepartment = $this->getDoctorsByDepartment();

        $patientsByDoctor = $this->getPatientsByDoctor();


        include "./views/dashboard/index.php";
    }

    private function getStatistics()
    {
        $stats = [];

        $query = "SELECT COUNT(*) as total FROM patients";
        $result = $this->db->query($query);
        $stats['total_patients'] = $result->fetch_assoc()['total'];

        $query = "SELECT COUNT(*) as total FROM doctors";
        $result = $this->db->query($query);
        $stats['total_doctors'] = $result->fetch_assoc()['total'];

        $query = "SELECT COUNT(*) as total FROM departments";
        $result = $this->db->query($query);
        $stats['total_departments'] = $result->fetch_assoc()['total'];

        $query = "SELECT COUNT(*) as total FROM patients";
        $result = $this->db->query($query);
        $stats['new_patients_month'] = $result->fetch_assoc()['total'];

        return $stats;
    }

    private function getRecentPatients($limit = 5)
    {
        $query = "SELECT * FROM patients LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    private function getDoctorsByDepartment()
    {
        $query = "SELECT d.department_name, COUNT(doc.doctor_id) as doctor_count 
                  FROM departments d 
                  LEFT JOIN doctors doc ON d.department_id = doc.department_id 
                  GROUP BY d.department_id, d.department_name
                  ORDER BY doctor_count DESC";
        $result = $this->db->query($query);
        return $result;
    }

    private function getPatientsByGender()
    {
        $query = "  SELECT  
                    count(case when gender='Male' then 1 end) as male_count,
                    count(case when gender='Female' then 1 end) as female_count
                    FROM patients";
        $result = $this->db->query($query);
        return $result;
    }

    private function getPatientsByDoctor()
    {
        $query = "SELECT CONCAT(d.first_name, ' ', d.last_name) as doctor_name, 
              COUNT(p.patient_id) as patient_count 
              FROM doctors d 
              LEFT JOIN patients p ON d.doctor_id = p.doctor_id 
              GROUP BY d.doctor_id
              ORDER BY patient_count DESC
              LIMIT 10";
        $result = $this->db->query($query);
        return $result;
    }
}
