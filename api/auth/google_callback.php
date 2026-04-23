<?php
/**
 * Handle Google OAuth 2.0 Callback
 */
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/google_oauth.php';

if (!isset($_GET['state']) || !isset($_SESSION['oauth_state']) || $_GET['state'] !== $_SESSION['oauth_state']) {
    die('Invalid state parameter. CSRF attempt blocked.');
}

if (!isset($_GET['code'])) {
    die('Authorization code not found. Login cancelled.');
}

$code = $_GET['code'];

// 1. Exchange code for token
$ch = curl_init('https://oauth2.googleapis.com/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code',
    'code' => $code
]));
$response = curl_exec($ch);
curl_close($ch);

$token_data = json_decode($response, true);
if (isset($token_data['error'])) {
    die('Error fetching access token: ' . $token_data['error_description']);
}

$access_token = $token_data['access_token'];

// 2. Fetch user profile
$ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $access_token]);
$profile_response = curl_exec($ch);
curl_close($ch);

$profile = json_decode($profile_response, true);
if (isset($profile['error'])) {
    die('Error fetching profile.');
}

$google_id = $profile['id'];
$email = $profile['email'];
$name = $profile['name'];
$avatar = $profile['picture'] ?? null;

$db = getDB();

// 3. Upsert User using PDO (Role mapped: 'client' = 'user')
$stmt = $db->prepare("SELECT id, role FROM users WHERE google_id = ? OR email = ?");
$stmt->execute([$google_id, $email]);
$user = $stmt->fetch();

if ($user) {
    // Update existing
    $stmt = $db->prepare("UPDATE users SET google_id = ?, name = ?, avatar = ?, last_login = NOW() WHERE id = ?");
    $stmt->execute([$google_id, $name, $avatar, $user['id']]);
    $user_id = $user['id'];
    $role = $user['role'];
} else {
    // Insert new (random secure password since they use OAuth)
    $stmt = $db->prepare("INSERT INTO users (google_id, name, email, avatar, role, password, created_at, last_login) VALUES (?, ?, ?, ?, 'user', 'oauth_placeholder', NOW(), NOW())");
    $stmt->execute([$google_id, $name, $email, $avatar]);
    $user_id = $db->lastInsertId();
    $role = 'user';
}

// 4. Session Setup
// session_regenerate_id(true); // Temporarily disabled to debug session loss

$_SESSION['user_id'] = $user_id;
$_SESSION['user_name'] = $name;
$_SESSION['user_email'] = $email;
$_SESSION['user_role'] = $role;
$_SESSION['user_avatar'] = $avatar;

// Redirect based on role logic
if ($role === 'consultant') {
    header('Location: ' . BASE_URL . 'consultant/dashboard');
} else {
    header('Location: ' . BASE_URL . 'user/dashboard');
}
session_write_close(); // Ensure session is saved before redirect
exit;
