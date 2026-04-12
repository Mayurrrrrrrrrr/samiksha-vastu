<?php
/**
 * Newsletter API Endpoint
 */
header('Content-Type: application/json');
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);

    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Invalid email']);
        exit;
    }

    // Check duplicate
    $check = $db->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        echo json_encode(['success' => true, 'message' => 'Already subscribed']);
        exit;
    }

    $db->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)")->execute([$email]);
    echo json_encode(['success' => true, 'message' => 'Subscribed successfully']);
    exit;
}

echo json_encode(['error' => 'Invalid request']);
