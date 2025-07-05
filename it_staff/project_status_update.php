<?php
require '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['role'] === 'staff') {
    $project_id = intval($_POST['project_id']);
    $status = $_POST['status'];
    $mainStatus = 'under_process';

    if (!in_array($status, ['fixed', 'need_support'])) {
        $_SESSION['error'] = "Invalid status selected.";
        header("Location: projects.php");
        exit;
    }

    // set mainStatus to needs_attention if status is need_support
    if ($status === 'need_support') {
        $mainStatus = 'needs_attention';
    }

    // Update the project status
    $stmt = $pdo->prepare("UPDATE projects SET status = ?, main_status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$status, $mainStatus, $project_id]);

    // Optional: notify admin of status update
    $adminIds = $pdo->query("SELECT id FROM users WHERE role = 'admin'")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($adminIds as $admin_id) {
        $notif = $pdo->prepare("INSERT INTO notifications (user_id, message, related_project_id, is_seen, created_at) VALUES (?, ?, ?, 0, NOW())");
        $notif->execute([$admin_id, "Project #$project_id status updated to '$status' by staff.", $project_id]);
    }

    $_SESSION['success'] = "Project status updated.";
    header("Location: projects.php");
    exit;
}
