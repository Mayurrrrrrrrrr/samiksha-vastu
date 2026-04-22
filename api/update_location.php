<?php
/**
 * Update Location API
 * Receives POST request with token, lat, lng, and optionally submission_id
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$token = $input['token'] ?? '';
$lat = $input['latitude'] ?? null;
$lng = $input['longitude'] ?? null;
$submission_id = $input['submission_id'] ?? null;

if (empty($token) && !isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($lat === null || $lng === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Latitude and longitude are required']);
    exit;
}

$db = getDB();

try {
    $userId = null;

    if (!empty($token)) {
        // Find user by token
        $stmt = $db->prepare("SELECT id FROM users WHERE location_token = ?");
        $stmt->execute([$token]);
        $userId = $stmt->fetchColumn();
        
        if (!$userId) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Invalid token']);
            exit;
        }
    } else {
        $userId = currentUserId();
    }

    // Update user's default location
    $updateUser = $db->prepare("UPDATE users SET latitude = ?, longitude = ?, location_updated_at = NOW() WHERE id = ?");
    $updateUser->execute([$lat, $lng, $userId]);

    // If submission_id is provided, update the submission as well
    if (!empty($submission_id)) {
        $updateSub = $db->prepare("UPDATE submissions SET latitude = ?, longitude = ? WHERE id = ? AND user_id = ?");
        $updateSub->execute([$lat, $lng, $submission_id, $userId]);
    }

    echo json_encode([
        'success' => true, 
        'message' => 'Location updated successfully',
        'data' => [
            'latitude' => $lat,
            'longitude' => $lng,
            'updated_at' => date('Y-m-d H:i:s')
        ]
    ]);

} catch (PDOException $e) {
    error_log("Location Update Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred while updating location']);
}
