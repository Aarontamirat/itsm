<?php
require '../config/db.php';

// Count user roles
$userCounts = $pdo->query("
  SELECT role, COUNT(*) as count 
  FROM users 
  GROUP BY role
")->fetchAll(PDO::FETCH_KEY_PAIR);

// Count incident statuses
$statusCounts = $pdo->query("
  SELECT status, COUNT(*) as count 
  FROM incidents 
  GROUP BY status
")->fetchAll(PDO::FETCH_KEY_PAIR);

// Get overall counts
$totalUsers = array_sum($userCounts);
$totalIncidents = array_sum($statusCounts);
$assignedCount = $pdo->query("SELECT COUNT(*) FROM incidents WHERE assigned_to IS NOT NULL")->fetchColumn();

echo json_encode([
  'userCounts' => $userCounts,
  'statusCounts' => $statusCounts,
  'totals' => [
    'users' => $totalUsers,
    'incidents' => $totalIncidents,
    'assigned' => $assignedCount
  ]
]);
