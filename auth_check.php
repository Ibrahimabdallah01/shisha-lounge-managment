<?php
// auth_check.php - Include this at the top of your existing dashboard and other protected pages
session_start();

// Include your database class and auth
require_once 'config/database.php';
require_once 'classes/Auth.php';

// Create database connection and auth instance
$database = new Database();
$pdo = $database->connect();
$auth = new Auth($pdo);

// Check if user is logged in, redirect to login if not
$auth->requireLogin();

// Get current user (you can use this in your existing pages)
$currentUser = $auth->getCurrentUser();
?>