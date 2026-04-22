<?php
include 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $conn->real_escape_string($_POST['phone']);
    $otp = $conn->real_escape_string($_POST['otp']);

    $check = $conn->query("SELECT * FROM users WHERE phone_number = '$phone' AND otp = '$otp'");

    if ($check->num_rows > 0) {
        $conn->query("UPDATE users SET is_verified = 1 WHERE phone_number = '$phone'");
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid Code"]);
    }
}
?>