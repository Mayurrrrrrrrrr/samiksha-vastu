<?php
/**
 * Chat API Endpoint
 */
header('Content-Type: application/json');
$db = getDB();

// Session check
if (!isLoggedIn()) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$uid = currentUserId();

// Handle GET - Poll for messages
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action === 'poll') {
        $afterId = intval($_GET['after'] ?? 0);
        $partnerId = intval($_GET['partner'] ?? 0);

        $stmt = $db->prepare("SELECT cm.id, cm.sender_id, cm.message, DATE_FORMAT(cm.created_at, '%h:%i %p') as time FROM chat_messages cm WHERE cm.id > ? AND ((cm.sender_id = ? AND cm.receiver_id = ?) OR (cm.sender_id = ? AND cm.receiver_id = ?)) ORDER BY cm.created_at ASC");
        $stmt->execute([$afterId, $uid, $partnerId, $partnerId, $uid]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mark as read
        if (!empty($messages)) {
            $db->prepare("UPDATE chat_messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ? AND is_read = 0")->execute([$uid, $partnerId]);
        }

        echo json_encode(['messages' => $messages]);
        exit;
    }

    if ($action === 'unread') {
        $count = $db->prepare("SELECT COUNT(*) FROM chat_messages WHERE receiver_id = ? AND is_read = 0");
        $count->execute([$uid]);
        echo json_encode(['count' => intval($count->fetchColumn())]);
        exit;
    }
}

// Handle POST - Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    if ($action === 'send') {
        $receiverId = intval($input['receiver_id'] ?? 0);
        $message = trim($input['message'] ?? '');

        if (!$receiverId || !$message) {
            echo json_encode(['error' => 'Invalid data']);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$uid, $receiverId, $message]);

        echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
        exit;
    }
}

echo json_encode(['error' => 'Invalid request']);
