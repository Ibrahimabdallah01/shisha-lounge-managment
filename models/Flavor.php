<?php

// models/Flavor.php - Flavor Model
class Flavor {
    private $conn;
    private $table = "flavors";

    public $id;
    public $name;
    public $stock;
    public $min_stock;
    public $price;
    public $popularity;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all flavors
    public function getAllFlavors() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY popularity DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Add new flavor
    public function addFlavor() {
        $query = "INSERT INTO " . $this->table . " 
                  SET name = :name, 
                      stock = :stock, 
                      min_stock = :min_stock, 
                      price = :price, 
                      popularity = :popularity";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->stock = htmlspecialchars(strip_tags($this->stock));
        $this->min_stock = htmlspecialchars(strip_tags($this->min_stock));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->popularity = 0;

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":stock", $this->stock);
        $stmt->bindParam(":min_stock", $this->min_stock);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":popularity", $this->popularity);

        return $stmt->execute();
    }

    // Update stock
    public function updateStock($flavor_id, $new_stock) {
        $query = "UPDATE " . $this->table . " 
                  SET stock = :stock 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":stock", $new_stock);
        $stmt->bindParam(":id", $flavor_id);

        return $stmt->execute();
    }

    // Get low stock flavors
    public function getLowStockFlavors() {
        $query = "SELECT * FROM " . $this->table . " WHERE stock <= min_stock";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}