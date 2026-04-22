<?php
require_once __DIR__ . '/../config/database.php';

echo "=== Vastu Samiksha Migration: 003 Add User Location ===\n\n";

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Add columns to users table
    $pdo->exec("ALTER TABLE `users` 
        ADD COLUMN `latitude` DECIMAL(10,8) DEFAULT NULL AFTER `last_login`,
        ADD COLUMN `longitude` DECIMAL(11,8) DEFAULT NULL AFTER `latitude`,
        ADD COLUMN `location_updated_at` DATETIME DEFAULT NULL AFTER `longitude`,
        ADD COLUMN `location_token` VARCHAR(64) DEFAULT NULL AFTER `location_updated_at`,
        ADD UNIQUE INDEX `idx_location_token` (`location_token`)");
    
    echo "✓ Added location fields to users table\n";

    // Generate location tokens for existing users
    $users = $pdo->query("SELECT id FROM users WHERE location_token IS NULL")->fetchAll();
    $stmt = $pdo->prepare("UPDATE users SET location_token = ? WHERE id = ?");
    $count = 0;
    foreach ($users as $u) {
        $token = bin2hex(random_bytes(16));
        $stmt->execute([$token, $u['id']]);
        $count++;
    }
    
    echo "✓ Generated location tokens for $count existing users\n";

    echo "\n=== Migration Complete! ===\n";

} catch (PDOException $e) {
    // If columns already exist, this might fail, so we catch and report silently or error
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Columns already exist. Skipping.\n";
    } else {
        echo "ERROR: " . $e->getMessage() . "\n";
        exit(1);
    }
}
