<?php
/**
 * Submit Blog Comment API
 */
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Verify CSRF
if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
    setFlash('error', t('csrf_failed', $_SESSION['lang'] ?? 'en') ?? 'Invalid security token.');
    $redirectInfo = $_POST['redirect_url'] ?? BASE_URL;
    header('Location: ' . $redirectInfo);
    exit;
}

$blog_id = filter_input(INPUT_POST, 'blog_id', FILTER_VALIDATE_INT);
$name = clean($_POST['name'] ?? '');
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$comment = clean($_POST['comment'] ?? '');
$redirectUrl = $_POST['redirect_url'] ?? BASE_URL;
$lang = $_SESSION['lang'] ?? 'en';

if (!$blog_id || !$name || !$email || !$comment) {
    setFlash('error', $lang === 'hi' ? 'कृपया सभी फ़ील्ड सही से भरें।' : 'Please fill all required fields correctly.');
    header('Location: ' . $redirectUrl);
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO blog_comments (blog_id, name, email, comment, is_approved) VALUES (?, ?, ?, ?, 0)");
    $stmt->execute([$blog_id, $name, $email, $comment]);

    setFlash('success', $lang === 'hi' ? 'आपकी टिप्पणी सबमिट कर दी गई है और अनुमोदन की प्रतीक्षा में है।' : 'Your comment has been submitted and is awaiting approval.');
} catch (PDOException $e) {
    setFlash('error', $lang === 'hi' ? 'टिप्पणी सबमिट करने में विफल।' : 'Failed to submit comment.');
}

header('Location: ' . $redirectUrl);
exit;
