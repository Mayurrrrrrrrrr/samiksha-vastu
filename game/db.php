<?php
// db.php - Central Database Configuration
$host = "sql206.infinityfree.com";
$user = "if0_40043837";
$pass = "9dxw7HGm504wn";
$db   = "if0_40043837_vastu";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>