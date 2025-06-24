<?php
// config/database.php - Database Configuration
class Database {
    private $host = "localhost";
    private $database_name = "shisha_lounge_db";
    private $username = "root";
    private $password = "";
    private $connection;

    public function connect() {
        $this->connection = null;
        
        try {
            $this->connection = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->database_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->connection;
    }
}
?>