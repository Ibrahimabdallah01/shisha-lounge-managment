<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Auth.php';

$database = new Database();
$pdo = $database->connect();
$auth = new Auth($pdo);

$auth->logout();
header('Location: login.php?message=logged_out');
exit();
?>