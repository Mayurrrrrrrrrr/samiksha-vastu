<?php
/**
 * Admin Login Handling
 */
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid Request Method: ' . $_SERVER['REQUEST_METHOD']);
}

if (empty($_POST)) {
    die('Error: POST data is empty. Your server might be stripping request bodies.');
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$db = getDB();

// We map the requested 'admin' role to 'consultant' to maintain legacy compatibility
$stmt = $db->prepare("SELECT id, password, name, role FROM users WHERE email = ? AND role = 'consultant'");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    // Session Regeneration for security
    session_regenerate_id(true);
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role'] = 'consultant';
    
    // Update last login
    $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
    
    header('Location: ' . BASE_URL . 'consultant/dashboard');
} else {
    setFlash('error', 'Invalid Admin Credentials');
    header('Location: ' . BASE_URL . 'login');
}
exit;
