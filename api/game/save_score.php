<?php
/**
 * Save Vastu Quiz Score API
 */
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['answers'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit;
}

$guest_name = $input['guest_name'] ?? 'Anonymous';
$time = (int)($input['time'] ?? 0);
$answers = $input['answers']; // Array of {id, answer}
$score = 0;

try {
    $db = getDB();
    
    // Evaluate score securely on server
    $qIds = array_column($answers, 'id');
    if (empty($qIds)) {
        echo json_encode(['success' => true, 'score' => 0]);
        exit;
    }
    
    $placeholders = str_repeat('?,', count($qIds) - 1) . '?';
    $stmt = $db->prepare("SELECT id, correct_option FROM game_questions WHERE id IN ($placeholders)");
    $stmt->execute($qIds);
    $correctAnswers = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    foreach ($answers as $ans) {
        $qId = $ans['id'];
        $userChoice = strtoupper(trim($ans['answer']));
        if (isset($correctAnswers[$qId]) && $correctAnswers[$qId] === $userChoice) {
            $score++;
        }
    }
    
    // Save to leaderboard
    $user_id = isLoggedIn() ? currentUserId() : null;
    if ($user_id) $guest_name = currentUserName(); // override if logged in
    
    $insertStmt = $db->prepare("INSERT INTO game_leaderboard (user_id, guest_name, score, total_time) VALUES (?, ?, ?, ?)");
    $insertStmt->execute([$user_id, $guest_name, $score, $time]);
    
    echo json_encode(['success' => true, 'score' => $score]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
