<?php
// classes/Auth.php - Authentication Class
class Auth {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    // Login user
    public function login($email, $password) {
        try {
            $stmt = $this->db->prepare("SELECT id, email, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['login_time'] = time();
                
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }
    
    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    // Logout user
    public function logout() {
        // Destroy session
        session_unset();
        session_destroy();
        
        return true;
    }
    
    // Require login (redirect if not authenticated)
    public function requireLogin($redirectTo = 'login.php') {
        if (!$this->isLoggedIn()) {
            header('Location: ' . $redirectTo);
            exit();
        }
    }
    
    // Get current user info
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare("SELECT id, email, created_at FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get user error: " . $e->getMessage());
            return false;
        }
    }
    
    // Create new user (for admin purposes)
    public function createUser($email, $password) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            return $stmt->execute([$email, $hashedPassword]);
        } catch (PDOException $e) {
            error_log("Create user error: " . $e->getMessage());
            return false;
        }
    }
    
    // Sanitize input
    public static function sanitizeInput($input) {
        return htmlspecialchars(strip_tags(trim($input)));
    }
}
?>