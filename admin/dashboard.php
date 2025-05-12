<?php
require_once '../config/db.php';

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = 1 AND seen = FALSE");
$stmt->execute();
$notifications = $stmt->fetchAll();

if (count($notifications) > 0) {
    echo '<div class="bg-red-600 text-white p-2 rounded mb-4">You have new notifications!</div>';
}


?>
<!DOCTYPE html>
<html>

<head>
    <title>Register Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 py-10">
    <!-- dont delete -->
    <a href="add_user.php" class="text-blue-600 underline">➕ Add New User</a>
</body>

</html>