<?php

// models/Customer.php - Customer Model
class Customer {
    private $conn;
    private $table = "customers";

    public $id;
    public $name;
    public $visits;
    public $last_visit;
    public $favorite_flavor;
    public $total_spent;
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all customers
    public function getAllCustomers() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY last_visit DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Add new customer
    public function addCustomer() {
        $query = "INSERT INTO " . $this->table . " 
                  SET name = :name, 
                      visits = :visits, 
                      last_visit = :last_visit, 
                      favorite_flavor = :favorite_flavor, 
                      total_spent = :total_spent, 
                      status = :status";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->visits = htmlspecialchars(strip_tags($this->visits));
        $this->last_visit = htmlspecialchars(strip_tags($this->last_visit));
        $this->favorite_flavor = htmlspecialchars(strip_tags($this->favorite_flavor));
        $this->total_spent = htmlspecialchars(strip_tags($this->total_spent));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":visits", $this->visits);
        $stmt->bindParam(":last_visit", $this->last_visit);
        $stmt->bindParam(":favorite_flavor", $this->favorite_flavor);
        $stmt->bindParam(":total_spent", $this->total_spent);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update customer visits
    public function updateVisits($customer_id) {
        $query = "UPDATE " . $this->table . " 
                  SET visits = visits + 1, 
                      last_visit = CURDATE() 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $customer_id);

        return $stmt->execute();
    }
}
