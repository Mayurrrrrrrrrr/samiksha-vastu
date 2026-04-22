<?php
/**
 * Google OAuth Configuration
 */
define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: 'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: 'YOUR_GOOGLE_CLIENT_SECRET');

// Make sure this exactly matches what is configured in Google Cloud Console
define('GOOGLE_REDIRECT_URI', 'https://samikshavastu.yuktaa.com/api/auth/google_callback');
