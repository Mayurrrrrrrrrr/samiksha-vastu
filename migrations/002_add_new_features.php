<?php
require_once __DIR__ . '/../config/database.php';

echo "=== Vastu Samiksha Migration: 002 New Features ===\n\n";

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Blog Comments
    $pdo->exec("CREATE TABLE IF NOT EXISTS `blog_comments` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `blog_id` INT UNSIGNED NOT NULL,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(150) NOT NULL,
        `comment` TEXT NOT NULL,
        `is_approved` TINYINT(1) DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`blog_id`) REFERENCES `blogs`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ blog_comments table\n";

    // Appointments
    $pdo->exec("CREATE TABLE IF NOT EXISTS `appointments` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT UNSIGNED DEFAULT NULL,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(150) NOT NULL,
        `phone` VARCHAR(20) NOT NULL,
        `appointment_date` DATE DEFAULT NULL,
        `status` ENUM('pending','confirmed','completed','cancelled') DEFAULT 'pending',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ appointments table\n";

    // Intake Forms
    $pdo->exec("CREATE TABLE IF NOT EXISTS `intake_forms` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT UNSIGNED NOT NULL,
        `appointment_id` INT UNSIGNED DEFAULT NULL,
        `persons_data` JSON DEFAULT NULL,
        `location_address` TEXT DEFAULT NULL,
        `google_earth_snapshot` VARCHAR(255) DEFAULT NULL,
        `other_fields` JSON DEFAULT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`appointment_id`) REFERENCES `appointments`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ intake_forms table\n";

    echo "\n=== Migration Complete! ===\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
