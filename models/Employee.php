<?php

// models/Employee.php - Employee Model
class Employee {
    private $conn;
    private $table = "employees";

    public $id;
    public $name;
    public $position;
    public $shift;
    public $services_count;
    public $salary;
    public $hire_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllEmployees() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function addEmployee() {
        $query = "INSERT INTO " . $this->table . " 
                  SET name = :name, 
                      position = :position, 
                      shift = :shift, 
                      services_count = 0, 
                      salary = :salary";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->position = htmlspecialchars(strip_tags($this->position));
        $this->shift = htmlspecialchars(strip_tags($this->shift));
        $this->salary = htmlspecialchars(strip_tags($this->salary));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":position", $this->position);
        $stmt->bindParam(":shift", $this->shift);
        $stmt->bindParam(":salary", $this->salary);

        return $stmt->execute();
    }

    public function updateSalary($employee_id, $new_salary) {
        $query = "UPDATE " . $this->table . " 
                  SET salary = :salary 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":salary", $new_salary);
        $stmt->bindParam(":id", $employee_id);

        return $stmt->execute();
    }
}
