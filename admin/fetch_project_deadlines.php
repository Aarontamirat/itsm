<?php
require '../config/db.php';

$stmt = $pdo->query("SELECT id, title, deadline_date, status FROM projects");
$projects = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $days_remaining = $row['deadline_date'] ? (strtotime($row['deadline_date']) - strtotime(date('Y-m-d'))) / (60*60*24) : null;
  $projects[] = [
    'title' => $row['title'],
    'days_remaining' => $days_remaining,
    'is_overdue' => $days_remaining !== null && $days_remaining < 0 && $row['status'] != 'fixed'
  ];
}
header('Content-Type: application/json');
echo json_encode(['projects' => $projects]);
