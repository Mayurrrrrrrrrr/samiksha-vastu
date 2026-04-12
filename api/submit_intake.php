<?php
/**
 * Submit Intake Form API
 */
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Verify CSRF
if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
    setFlash('error', t('csrf_failed', $_SESSION['lang'] ?? 'en') ?? 'Invalid security token.');
    header('Location: ' . BASE_URL . 'intake-form');
    exit;
}

if (!isLoggedIn()) {
    setFlash('error', 'Login required.');
    header('Location: ' . BASE_URL . 'login');
    exit;
}

$user_id = $_SESSION['user_id'];
$location_address = clean($_POST['location_address'] ?? '');
$persons_data = $_POST['persons'] ?? [];
$other_concerns = clean($_POST['other_concerns'] ?? '');

$persons = [];
if (is_array($persons_data)) {
    foreach ($persons_data as $p) {
        $persons[] = [
            'name' => clean($p['name'] ?? ''),
            'dob' => clean($p['dob'] ?? ''),
            'place_of_birth' => clean($p['place_of_birth'] ?? ''),
            'time_of_birth' => clean($p['time_of_birth'] ?? '')
        ];
    }
}

// Handle File Upload
$snapshot_path = null;
if (isset($_FILES['google_earth_snapshot']) && $_FILES['google_earth_snapshot']['error'] === UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['google_earth_snapshot']['tmp_name'];
    $name = basename($_FILES['google_earth_snapshot']['name']);
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    
    $allowed_exts = ['jpg', 'jpeg', 'png', 'pdf'];
    if (in_array($ext, $allowed_exts) && $_FILES['google_earth_snapshot']['size'] <= MAX_UPLOAD_SIZE) {
        if (!is_dir(UPLOADS_DIR)) {
            mkdir(UPLOADS_DIR, 0755, true);
        }
        $new_name = 'snapshot_' . $user_id . '_' . time() . '.' . $ext;
        if (move_uploaded_file($tmp_name, UPLOADS_DIR . $new_name)) {
            $snapshot_path = $new_name;
        }
    }
}

try {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO intake_forms (user_id, persons_data, location_address, google_earth_snapshot, other_fields) VALUES (?, ?, ?, ?, ?)");
    
    $other_json = json_encode(['concerns' => $other_concerns]);
    $persons_json = json_encode($persons);
    
    $stmt->execute([$user_id, $persons_json, $location_address, $snapshot_path, $other_json]);

    setFlash('success', 'Your intake form has been submitted successfully. We will review your details.');
} catch (PDOException $e) {
    setFlash('error', 'Failed to submit intake form.');
}

header('Location: ' . BASE_URL . 'user/dashboard');
exit;
