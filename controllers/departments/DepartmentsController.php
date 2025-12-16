<?php

namespace Controllers;

use Models\Department;

require_once "./modules/departments/Department.php";

class DepartmentController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function index()
    {
        $department = new Department($this->db);
        $departments = $department->readAllDepartment();
        include "./views/departments/list.php";
    }

    public function create()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $department = new Department($this->db);

            $department_name = trim($_POST['department_name']);
            $location = trim($_POST['location']);

            $errors = [];

            if (empty($department_name)) {
                $errors[] = "Department name is required.";
            }

            if (empty($location)) {
                $errors[] = "Department location is required.";
            }

            if (!empty($errors)) {
                include("./views/departments/create.php");
                return;
            }

            $department->department_name = $department_name;
            $department->location = $location;

            $result = $department->createDepartment();
            
            if ($result === false) {
                $errors[] = "Department with this name already exists.";
                include("./views/departments/create.php");
                return;
            }

            if ($result) {
                header("LOCATION: index.php?controller=departments&success=1");
                exit;
            } else {
                $error = "Failed to create department";
                include("./views/departments/create.php");
                return;
            }
        }

        include("./views/departments/create.php");
    }

    public function edit($id)
    {
        $department = new Department($this->db);
        $department->department_id = $id;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $department->department_name = trim($_POST['department_name']);
            $department->location = trim($_POST['location']);

            if ($department->updateDepartment()) {
                header("LOCATION: index.php?controller=departments&success=1");
                exit;
            } else {
                $error = "Failed to update department";
            }
        }

        $data = $department->readOneDepartment();
        include("./views/departments/edit.php");
    }

    public function delete($id)
    {
        $department = new Department($this->db);
        $department->department_id = $id;

        if ($department->deleteDepartment()) {
            header("LOCATION: index.php?controller=departments&success=1");
        } else {
            header("LOCATION: index.php?controller=departments&error=delete_failed");
        }
        exit;
    }
}