<?php
/**
 * Submission API Endpoint
 */
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    if ($action === 'update_status' && isConsultant()) {
        $id = intval($input['id'] ?? 0);
        $status = $input['status'] ?? '';
        $allowed = ['pending', 'in_progress', 'completed'];

        if (!in_array($status, $allowed)) {
            echo json_encode(['error' => 'Invalid status']);
            exit;
        }

        $db->prepare("UPDATE submissions SET status = ? WHERE id = ?")->execute([$status, $id]);
        echo json_encode(['success' => true]);
        exit;
    }
}

echo json_encode(['error' => 'Invalid request']);
