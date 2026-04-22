<?php
/**
 * Initiate Google OAuth 2.0 flow
 */
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/google_oauth.php';

// Generate a random token for CSRF protection
$_SESSION['oauth_state'] = bin2hex(random_bytes(16));

// Build the OAuth URL
$params = [
    'client_id' => GOOGLE_CLIENT_ID,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope' => 'openid email profile',
    'state' => $_SESSION['oauth_state'],
    'access_type' => 'online'
];

$auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);

// Redirect to Google
header('Location: ' . $auth_url);
exit;
