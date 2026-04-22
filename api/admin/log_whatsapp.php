<?php
/**
 * Log WhatsApp outbounds from Admin Panel
 */
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

if (!isConsultant()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

$client_id = $_POST['client_id'] ?? null;
$client_name = $_POST['client_name'] ?? '';
$client_mobile = $_POST['client_mobile'] ?? '';
$template_id = $_POST['template_id'] ?? null;
if (empty($template_id)) $template_id = null;
$message_sent = $_POST['message_sent'] ?? '';

try {
    $db = getDB();
    $stmt = $db->prepare("
        INSERT INTO whatsapp_message_log 
        (client_id, client_name, client_mobile, template_id, message_sent) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$client_id, $client_name, $client_mobile, $template_id, $message_sent]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log("WhatsApp Log Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
