<?php

// models/Payment.php - Payment Model
class Payment {
    private $conn;
    private $table = "payments";

    public $id;
    public $customer_name;
    public $amount;
    public $payment_method;
    public $transaction_date;
    public $transaction_time;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllPayments() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY transaction_date DESC, transaction_time DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function addPayment() {
        $query = "INSERT INTO " . $this->table . " 
                  SET customer_name = :customer_name, 
                      amount = :amount, 
                      payment_method = :payment_method, 
                      transaction_date = :transaction_date, 
                      transaction_time = :transaction_time";

        $stmt = $this->conn->prepare($query);

        $this->customer_name = htmlspecialchars(strip_tags($this->customer_name));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->payment_method = htmlspecialchars(strip_tags($this->payment_method));
        $this->transaction_date = htmlspecialchars(strip_tags($this->transaction_date));
        $this->transaction_time = htmlspecialchars(strip_tags($this->transaction_time));

        $stmt->bindParam(":customer_name", $this->customer_name);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":payment_method", $this->payment_method);
        $stmt->bindParam(":transaction_date", $this->transaction_date);
        $stmt->bindParam(":transaction_time", $this->transaction_time);

        return $stmt->execute();
    }

    public function getDailyRevenue($date) {
        $query = "SELECT SUM(amount) as total FROM " . $this->table . " WHERE transaction_date = :date";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":date", $date);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ? $row['total'] : 0;
    }
}