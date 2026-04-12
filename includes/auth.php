<?php
/**
 * Authentication System - Vastu Samiksha
 */

// Harden session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

// Set language from session/cookie/default
if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGS)) {
    $_SESSION['lang'] = $_GET['lang'];
    setcookie('lang', $_GET['lang'], time() + 86400 * 365, '/');
} elseif (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = $_COOKIE['lang'] ?? DEFAULT_LANG;
}

// Make $lang available globally for all pages
$lang = $_SESSION['lang'] ?? DEFAULT_LANG;

/**
 * Register a new user
 */
function registerUser($name, $email, $password, $phone = '', $dob = null, $gender = '')
{
    $db = getDB();

    // Check existing email
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Email already registered'];
    }

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    $stmt = $db->prepare("INSERT INTO users (name, email, password, phone, dob, gender, role, created_at) VALUES (?, ?, ?, ?, ?, ?, 'user', NOW())");
    $stmt->execute([$name, $email, $hash, $phone, $dob, $gender]);

    $userId = $db->lastInsertId();
    loginSession($userId, $name, $email, 'user');

    return ['success' => true, 'user_id' => $userId];
}

/**
 * Login user
 */
function loginUser($email, $password)
{
    $db = getDB();

    $stmt = $db->prepare("SELECT id, name, email, password, role, avatar FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }

    // Update last login
    $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);

    loginSession($user['id'], $user['name'], $user['email'], $user['role'], $user['avatar']);

    return ['success' => true, 'role' => $user['role']];
}

/**
 * Set session variables
 */
function loginSession($id, $name, $email, $role, $avatar = null)
{
    $_SESSION['user_id'] = $id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role'] = $role;
    $_SESSION['user_avatar'] = $avatar;
    $_SESSION['logged_in'] = true;
    session_regenerate_id(true);
}

/**
 * Logout user
 */
function logoutUser()
{
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"]);
    }
    session_destroy();
}

/**
 * Check if user is logged in
 */
function isLoggedIn()
{
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Check if current user is consultant (admin)
 */
function isConsultant()
{
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'consultant';
}

/**
 * Check if current user is regular user
 */
function isUser()
{
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'user';
}

/**
 * Get current user ID
 */
function currentUserId()
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user name
 */
function currentUserName()
{
    return $_SESSION['user_name'] ?? 'Guest';
}

/**
 * Get current user info
 */
function currentUser()
{
    if (!isLoggedIn())
        return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT id, name, email, phone, dob, gender, avatar, role, created_at FROM users WHERE id = ?");
    $stmt->execute([currentUserId()]);
    return $stmt->fetch();
}

/**
 * Require login - redirect if not logged in
 */
function requireLogin()
{
    if (!isLoggedIn()) {
        setFlash('warning', t('login_required'));
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
}

/**
 * Require consultant role
 */
function requireConsultant()
{
    requireLogin();
    if (!isConsultant()) {
        header('Location: ' . BASE_URL . 'user/dashboard');
        exit;
    }
}

/**
 * Require user role
 */
function requireUser()
{
    requireLogin();
    if (!isUser()) {
        header('Location: ' . BASE_URL . 'consultant/dashboard');
        exit;
    }
}

/**
 * Upload file helper
 */
function uploadFile($file, $directory = 'general', $allowedTypes = null)
{
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload failed'];
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'message' => 'File too large (max 10MB)'];
    }

    if ($allowedTypes && !in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }

    $uploadDir = UPLOADS_DIR . $directory . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $filepath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename, 'path' => $directory . '/' . $filename];
    }

    return ['success' => false, 'message' => 'Failed to save file'];
}
