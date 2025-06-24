<?php
// create_user.php - Run this once to create your admin user
require_once 'config/database.php';
require_once 'classes/Auth.php';

// Create database connection and auth instance
$database = new Database();
$pdo = $database->connect();
$auth = new Auth($pdo);

// Create admin user
$email = 'admin@ibrah.com';
$password = 'ibrah123'; // Change this to your desired password

if ($auth->createUser($email, $password)) {
    echo "✅ Admin user created successfully!<br>";
    echo "Email: " . $email . "<br>";
    echo "Password: " . $password . "<br><br>";
    echo "⚠️ Remember to change the password after first login!<br>";
    echo "<a href='login.php'>Go to Login</a>";
} else {
    echo "❌ Failed to create user. User might already exist.";
}
?>