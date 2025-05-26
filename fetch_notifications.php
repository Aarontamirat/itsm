<?php
session_start();
require 'config/db.php';

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role']; // Assuming you store role in session

// Fetch notifications for the current user
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_seen = 0 ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
  'notifications' => $notifications,
  'user_role' => $user_role
]);
