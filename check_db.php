<?php
require_once __DIR__ . '/includes/auth.php';

echo "<h1>Session Info</h1>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

$db = getDB();

try {
    $stmt = $db->query("SELECT id, name, email, role, last_login FROM users");
    $users = $stmt->fetchAll();

    echo "<h1>User List</h1>";
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Last Login</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . $user['name'] . "</td>";
        echo "<td>" . $user['email'] . "</td>";
        echo "<td>" . $user['role'] . "</td>";
        echo "<td>" . $user['last_login'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
