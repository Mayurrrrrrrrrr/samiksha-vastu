<?php
/**
 * Quiz API Endpoint
 */
header('Content-Type: application/json');
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    if ($action === 'save_attempt') {
        $quizId = intval($input['quiz_id'] ?? 0);
        $playerName = trim($input['player_name'] ?? 'Anonymous');
        $score = intval($input['score'] ?? 0);
        $totalPoints = intval($input['total_points'] ?? 0);
        $timeTaken = intval($input['time_taken'] ?? 0);
        $userId = isLoggedIn() ? currentUserId() : null;

        $stmt = $db->prepare("INSERT INTO quiz_attempts (quiz_id, user_id, player_name, score, total_points, time_taken, completed_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$quizId, $userId, $playerName, $score, $totalPoints, $timeTaken]);

        echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
        exit;
    }
}

echo json_encode(['error' => 'Invalid request']);
