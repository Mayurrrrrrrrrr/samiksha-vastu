<?php
// debug.php - Vastu Kiosk System Diagnostic
require_once 'config.php';

echo "<h2>🛡️ Vastu Kiosk Health Check</h2>";
echo "<pre>";

// 1. Test Database Connection
if ($conn->ping()) {
    echo "✅ SUCCESS: Database Connection is LIVE.\n";
} else {
    echo "❌ ERROR: Database Connection Failed: " . $conn->error . "\n";
}

// 2. Check Table Existence
$tables = ['users', 'questions', 'choices'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        $count = $conn->query("SELECT COUNT(*) FROM $table")->fetch_row();
        echo "✅ TABLE FOUND: '$table' (Rows: $count[0])\n";
    } else {
        echo "❌ MISSING TABLE: '$table' is not found in the database.\n";
    }
}

// 3. Check Directory Permissions
$upload_dir = 'uploads/';
if (is_dir($upload_dir)) {
    if (is_writable($upload_dir)) {
        echo "✅ FOLDER WRITABLE: '$upload_dir' is ready for image uploads.\n";
    } else {
        echo "❌ PERMISSION DENIED: '$upload_dir' is not writable (Set to 777 in WinSCP).\n";
    }
} else {
    echo "❌ MISSING FOLDER: '$upload_dir' directory does not exist.\n";
}

// 4. Check Site URL Configuration
if (isset($site_url)) {
    echo "✅ CONFIG FOUND: Site URL is set to " . $site_url . "\n";
} else {
    echo "❌ CONFIG MISSING: \$site_url is not defined in config.php.\n";
}

// 5. Environment Info
echo "\n--- Environment Info ---\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";

echo "</pre>";
?>