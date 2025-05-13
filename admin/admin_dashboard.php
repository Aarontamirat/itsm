<?php
require_once '../config/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = 1 AND seen = FALSE");
$stmt->execute();
$notifications = $stmt->fetchAll();

if (count($notifications) > 0) {
    $NotificationCount = count($notifications);
} else {
    $NotificationCount = 0;
}


?>
<!DOCTYPE html>
<html>

<head>
    <title>Register Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <!-- assign page link -->
<!-- <a href="assign_incidents.php" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Assign</a> -->

<!-- Generate reports -->
<!-- <h2 class="text-2xl font-semibold mb-4">Generate Reports</h2>
<div class="space-x-4">
    <a href="generate_incident_report.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Generate Incident Report</a>
    <a href="generate_user_report.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Generate User Report</a>
</div> -->



</body>

</html>