<?php
/**
 * Emergency Admin Setup Script
 * DELETE THIS FILE AFTER USE!
 */
require_once __DIR__ . '/includes/auth.php';

$db = getDB();

$adminEmail = 'admin@samikshavastu.com';
$adminPass = 'Admin@1234'; // Default temporary password
$hashedPass = password_hash($adminPass, PASSWORD_BCRYPT, ['cost' => 12]);

try {
    // Check if user exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);
    $user = $stmt->fetch();

    if ($user) {
        // Update existing admin
        $stmt = $db->prepare("UPDATE users SET password = ?, role = 'consultant' WHERE id = ?");
        $stmt->execute([$hashedPass, $user['id']]);
        echo "Admin user updated successfully.<br>";
    } else {
        // Create new admin
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES ('Admin', ?, ?, 'consultant', NOW())");
        $stmt->execute([$adminEmail, $hashedPass]);
        echo "Admin user created successfully.<br>";
    }

    echo "Email: $adminEmail<br>";
    echo "Password: $adminPass<br>";
    echo "Role: consultant<br>";
    echo "<br><b>IMPORTANT: Delete this file (setup_admin.php) immediately after login!</b>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
