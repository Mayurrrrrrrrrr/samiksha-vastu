<?php
include 'config.php';
header('Content-Type: application/json');

// 1. Fetch unverified joiners from the last 1 minute
$new_users = $conn->query("SELECT name, otp FROM users WHERE is_verified = 0 AND created_at >= NOW() - INTERVAL 1 MINUTE ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

// 2. Fetch Top 5 High Scores
$top_scores = $conn->query("SELECT name, final_score, persona, badge FROM users WHERE is_verified = 1 ORDER BY final_score DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

echo json_encode(["new_users" => $new_users, "top_scores" => $top_scores]);
?>