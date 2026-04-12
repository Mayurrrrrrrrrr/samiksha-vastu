<?php
/**
 * Image Upload API for TinyMCE Editor
 * Handles drag-and-drop and button image uploads in the blog editor
 */

header('Content-Type: application/json');

// Require consultant authentication
if (!isLoggedIn() || !isConsultant()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Verify CSRF
if (!verifyCSRF($_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''))) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

if (empty($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

$file = $_FILES['file'];

// Validate
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'Upload failed with error code: ' . $file['error']]);
    exit;
}

if ($file['size'] > MAX_UPLOAD_SIZE) {
    http_response_code(400);
    echo json_encode(['error' => 'File too large (max 10MB)']);
    exit;
}

// Validate MIME type
$allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedMimes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Allowed: JPEG, PNG, WebP, GIF, SVG']);
    exit;
}

// Double-check extension
$allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExts)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file extension']);
    exit;
}

// Upload
$uploadDir = UPLOADS_DIR . 'blogs/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$filename = uniqid('img_') . '_' . time() . '.' . $ext;
$filepath = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $filepath)) {
    // Return the URL for TinyMCE
    echo json_encode([
        'location' => UPLOADS_URL . 'blogs/' . $filename
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save file']);
}
