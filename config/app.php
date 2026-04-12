<?php
/**
 * Application Configuration - Vastu Samiksha
 */

// Site Info
define('SITE_NAME', 'Vastu Samiksha');
define('SITE_TAGLINE', 'Transform Your Space, Transform Your Life');
define('SITE_TAGLINE_HI', 'अपने स्थान को बदलें, अपने जीवन को बदलें');
define('CONSULTANT_NAME', 'Samiksha Dubey');
define('CONSULTANT_NAME_HI', 'समीक्षा दुबे');
define('SITE_EMAIL', 'contact@vastusamiksha.com');
define('SITE_PHONE', '+91 70002 08511');
define('SITE_ADDRESS', 'New Delhi, India');

// Base URL - adjust for your server
define('BASE_URL', 'https://samikshavastu.yuktaa.com/');
define('ASSETS_URL', BASE_URL . 'assets/');
define('UPLOADS_URL', BASE_URL . 'assets/uploads/');
define('UPLOADS_DIR', __DIR__ . '/../assets/uploads/');

// Session config
define('SESSION_LIFETIME', 86400 * 7); // 7 days

// Pagination
define('POSTS_PER_PAGE', 9);
define('MESSAGES_PER_PAGE', 50);

// File upload limits
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('ALLOWED_DOC_TYPES', ['application/pdf', 'image/jpeg', 'image/png']);
define('ALLOWED_EBOOK_TYPES', ['application/pdf', 'application/epub+zip']);

// Social Media Links
define('SOCIAL_FACEBOOK', 'https://facebook.com/vastusamiksha');
define('SOCIAL_INSTAGRAM', 'https://instagram.com/vastusamiksha');
define('SOCIAL_YOUTUBE', 'https://youtube.com/@vastusamiksha');
define('SOCIAL_TWITTER', 'https://twitter.com/vastusamiksha');
define('SOCIAL_WHATSAPP', 'https://wa.me/917000208511');
define('SOCIAL_TELEGRAM', 'https://t.me/vastusamiksha');

// SEO Defaults
define('META_DESCRIPTION', 'Vastu Samiksha - Expert Vastu Shastra & Numerology consultations by Samiksha Dubey. Transform your home, office, and life with ancient Indian wisdom.');
define('META_DESCRIPTION_HI', 'वास्तु समीक्षा - समीक्षा दुबे द्वारा विशेषज्ञ वास्तु शास्त्र और अंक ज्योतिष परामर्श। प्राचीन भारतीय ज्ञान से अपने घर, कार्यालय और जीवन को बदलें।');
define('META_KEYWORDS', 'vastu shastra, numerology, vastu consultant, vastu tips, numerology calculator, indian astrology, vastu for home, vastu for office, samiksha dubey');

// Language
define('DEFAULT_LANG', 'en');
define('SUPPORTED_LANGS', ['en', 'hi']);
define('DEFAULT_BLOG_LANG', 'hi'); // Default language for displaying blogs

// Translation strings
function t($key, $lang = null)
{
    if ($lang === null) {
        $lang = $_SESSION['lang'] ?? DEFAULT_LANG;
    }

    static $translations = null;
    if ($translations === null) {
        $translations = include __DIR__ . '/../includes/translations.php';
    }

    return $translations[$lang][$key] ?? $translations['en'][$key] ?? $key;
}

// Flash messages
function setFlash($type, $message)
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// CSRF Protection
function generateCSRF()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRF($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField()
{
    return '<input type="hidden" name="csrf_token" value="' . generateCSRF() . '">';
}

// Sanitize
function clean($str)
{
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function slugify($text)
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = preg_replace('~[^-\w]+~', '', transliterator_transliterate('Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove', $text));
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text);
}

// Time ago helper
function timeAgo($datetime)
{
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0)
        return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0)
        return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0)
        return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0)
        return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0)
        return $diff->i . ' min' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'Just now';
}

// JSON response helper
function jsonResponse($data, $code = 200)
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
