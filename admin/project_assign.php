<?php
require '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['role'] === 'admin') {
    $project_id = intval($_POST['project_id']);
    $assigned_to = intval($_POST['assigned_to']);

    $stmt = $pdo->prepare("UPDATE projects SET assigned_to = ?, status = 'assigned', assigned_at = NOW(), updated_at = NOW() WHERE id = ?");
    $stmt->execute([$assigned_to, $project_id]);

    // Optional: Insert notification for the staff
    $notif = $pdo->prepare("INSERT INTO notifications (user_id, message, related_project_id, is_seen, created_at) VALUES (?, ?, ?, 0, NOW())");
    $notif->execute([$assigned_to, "You have been assigned a new project (ID: $project_id)", $project_id]);

    $_SESSION['success'] = "Project successfully assigned.";
    header("Location: projects.php");
    exit;
}
