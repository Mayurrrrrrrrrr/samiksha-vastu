<?php
include 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $otp = rand(1000, 9999);

    // Updates existing entry or creates new one to prevent duplicate phone numbers
    $sql = "INSERT INTO users (name, phone_number, otp, created_at, is_verified) 
            VALUES ('$name', '$phone', '$otp', NOW(), 0)
            ON DUPLICATE KEY UPDATE otp = '$otp', created_at = NOW(), is_verified = 0";

    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "OTP Generated"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}
?>