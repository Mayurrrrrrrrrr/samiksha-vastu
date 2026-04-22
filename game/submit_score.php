<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $conn->real_escape_string($_POST['phone']);
    $score = intval($_POST['score']);
    
    // Assign Award-Winning Titles
    $persona = ($score >= 100) ? "Master Architect" : "Energy Explorer";
    $badge = ($score >= 100) ? "🏆" : "🌟";

    $conn->query("UPDATE users SET final_score = $score, persona = '$persona', badge = '$badge' WHERE phone_number = '$phone'");
}
?>