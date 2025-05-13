<?php
// dashboard_data.php
require '../config/db.php';

$data = [];

// Count users by role
$userRoles = ['admin', 'staff', 'user'];
foreach ($userRoles as $role) {
  $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE role = ?");
  $stmt->bindParam(1, $role, PDO::PARAM_STR);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  $data['users'][$role] = $result['count'];
}

// Count incidents by status
$statuses = ['fixed', 'pending', 'not fixed', 'assigned'];
foreach ($statuses as $status) {
  $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM incidents WHERE status = ?");
  $stmt->bindParam(1, $status, PDO::PARAM_STR);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  $data['incidents'][$status] = $result['count'];
}

header('Content-Type: application/json');
echo json_encode($data);
?>
