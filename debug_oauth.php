<?php
require_once __DIR__ . '/config/google_oauth.php';

echo "GOOGLE_CLIENT_ID: " . GOOGLE_CLIENT_ID . "<br>";
echo "GOOGLE_REDIRECT_URI: " . GOOGLE_REDIRECT_URI . "<br>";
echo "ENV GOOGLE_CLIENT_ID: " . (getenv('GOOGLE_CLIENT_ID') ?: 'NOT SET') . "<br>";
?>
