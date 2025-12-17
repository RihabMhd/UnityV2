<?php

namespace Controllers;

use Models\Patient;

require_once "./modules/patients/Patient.php";

class PatientController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function index()
    {
        $patient = new Patient($this->db);
        $patients = $patient->readAllPatient();
        include "./views/patients/list.php";
    }

    public function create()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $patient = new Patient($this->db);

            $first_name = trim($_POST['first_name']);
            $last_name = trim($_POST['last_name']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone_number']);

            $errors = [];

            if (empty($first_name)) {
                $errors[] = "First name is required.";
            }

            if (empty($last_name)) {
                $errors[] = "Last name is required.";
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email address.";
            }

            if (!preg_match("/^[0-9]{10}$/", $phone)) {
                $errors[] = "Phone number must be 10 digits.";
            }

    
            if (!empty($errors)) {
                include("./views/patients/create.php");
                return; 
            }
           
            $patient->first_name = $first_name;
            $patient->last_name = $last_name;
            $patient->gender = trim($_POST['gender']);
            $patient->address = trim($_POST['address']);
            $patient->date_of_birth = trim($_POST['date_of_birth']);
            $patient->phone_number = $phone;
            $patient->email = $email;

            if ($patient->createPatient()) {
                header("LOCATION: index.php?controller=patients&success=1");
                exit;
            } else {
                $error = "Failed to create patient";
            }
        }

        include("./views/patients/create.php");
    }

    public function edit($id)
    {
        $patient = new Patient($this->db);
        $patient->patient_id = $id;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $patient->first_name = trim($_POST['first_name']);
            $patient->last_name = trim($_POST['last_name']);
            $patient->gender = trim($_POST['gender']);
            $patient->address = trim($_POST['address']);
            $patient->date_of_birth = trim($_POST['date_of_birth']);
            $patient->phone_number = trim($_POST['phone_number']);
            $patient->email = trim($_POST['email']);

            if ($patient->updatePatient()) {
                header("LOCATION: index.php?controller=patients&success=1");
                exit;
            } else {
                $error = "Failed to update patient";
            }
        }

        $data = $patient->readOnePatient();
        include("./views/patients/edit.php");
    }

    public function delete($id)
    {
        $patient = new Patient($this->db);
        $patient->patient_id = $id;

        if ($patient->deletePatient()) {
            header("LOCATION: index.php?controller=patients&success=1");
        } else {
            header("LOCATION: index.php?error=delete_failed");
        }
        exit;
    }
}
