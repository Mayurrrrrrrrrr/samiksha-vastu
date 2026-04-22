<?php
/**
 * Vastu Quiz Leaderboard API
 */
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

try {
    $db = getDB();
    
    // Top 10 by score (desc), then total_time (asc)
    $stmt = $db->query("
        SELECT guest_name as name, score, total_time, played_at 
        FROM game_leaderboard 
        ORDER BY score DESC, total_time ASC 
        LIMIT 10
    ");
    $leaders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($leaders);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
