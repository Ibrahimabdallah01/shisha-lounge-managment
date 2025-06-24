<?php

// models/Inventory.php - Inventory Model
class Inventory {
    private $conn;
    private $table = "inventory";

    public $id;
    public $item_name;
    public $stock;
    public $unit;
    public $min_stock;
    public $last_updated;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllItems() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY item_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function addItem() {
        $query = "INSERT INTO " . $this->table . " 
                  SET item_name = :item_name, 
                      stock = :stock, 
                      unit = :unit, 
                      min_stock = :min_stock";

        $stmt = $this->conn->prepare($query);

        $this->item_name = htmlspecialchars(strip_tags($this->item_name));
        $this->stock = htmlspecialchars(strip_tags($this->stock));
        $this->unit = htmlspecialchars(strip_tags($this->unit));
        $this->min_stock = htmlspecialchars(strip_tags($this->min_stock));

        $stmt->bindParam(":item_name", $this->item_name);
        $stmt->bindParam(":stock", $this->stock);
        $stmt->bindParam(":unit", $this->unit);
        $stmt->bindParam(":min_stock", $this->min_stock);

        return $stmt->execute();
    }

    public function updateStock($item_id, $new_stock) {
        $query = "UPDATE " . $this->table . " 
                  SET stock = :stock, 
                      last_updated = NOW() 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":stock", $new_stock);
        $stmt->bindParam(":id", $item_id);

        return $stmt->execute();
    }
}