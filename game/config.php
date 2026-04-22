<?php
// config.php - Central Database and System Settings
error_reporting(E_ALL);
ini_set('display_errors', 1); 

// Use the credentials you provided
$host = "sql206.infinityfree.com";
$user = "if0_40043837";
$pass = "9dxw7HGm504wn";
$db   = "if0_40043837_vastu";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Update this to your live URL
$site_url = "http://vastu-quiz.fwh.is"; 
?>