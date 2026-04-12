<?php
/**
 * Submit Appointment API
 */
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Verify CSRF
if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
    setFlash('error', t('csrf_failed', $_SESSION['lang'] ?? 'en') ?? 'Invalid security token.');
    header('Location: ' . BASE_URL . 'book-appointment');
    exit;
}

if (!isLoggedIn()) {
    setFlash('error', 'Login required.');
    header('Location: ' . BASE_URL . 'login');
    exit;
}

$user_id = $_SESSION['user_id'];
$name = clean($_POST['name'] ?? '');
$phone = clean($_POST['phone'] ?? '');
$preferred_date = clean($_POST['preferred_date'] ?? '');
$type = clean($_POST['type'] ?? '');
$description = clean($_POST['description'] ?? '');

if (!$name || !$phone) {
    setFlash('error', 'Please fill all required fields.');
    header('Location: ' . BASE_URL . 'book-appointment');
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO appointments (user_id, name, email, phone, appointment_date, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$user_id, $name, $_SESSION['user_email'] ?? '', $phone, $preferred_date ?: null]);

    // Also could map type and description to another system, or just append it as a note, for now we keep it simple.

    setFlash('success', 'Your appointment request has been submitted successfully. We will contact you soon.');
} catch (PDOException $e) {
    setFlash('error', 'Failed to submit appointment request.');
}

header('Location: ' . BASE_URL . 'user/dashboard');
exit;
