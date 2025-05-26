<?php
session_start();
require 'config/db.php';
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("UPDATE notifications SET is_seen = 1 WHERE user_id = ?");
$stmt->execute([$user_id]);

echo json_encode(['success' => true]);
