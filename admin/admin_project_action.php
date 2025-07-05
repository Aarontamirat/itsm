<?php
require '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['role'] === 'admin') {
  $project_id = intval($_POST['project_id']);
  $action = $_POST['action'];

  if (!in_array($action, ['confirm', 'redo'])) {
    $_SESSION['error'] = "Invalid action.";
    header("Location: admin_project_review.php");
    exit;
  }

  $newStatus = $action === 'confirm' ? 'confirmed fixed' : 'needs redo';
  $mainStatus = $action === 'confirm' ? 'completed' : 'under_process';

  // if new status is needs redo, we should make sure that we have a remark
  if ($newStatus === 'needs redo' && empty($_POST['reason'])) {
    $_SESSION['error'] = "Please provide a remark.";
    header("Location: admin_project_review.php");
    exit;
  }
  $remark = isset($_POST['reason']) ? trim($_POST['reason']) : '';
  
  if ($newStatus === 'needs redo' && !empty($remark)) {
    $stmt = $pdo->prepare("UPDATE projects SET status = ?, main_status = ?, remark = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$newStatus, $mainStatus, $remark, $project_id]);
  } else if ($newStatus === 'confirmed fixed') {
    $stmt = $pdo->prepare("UPDATE projects SET status = ?, main_status = ?, completion_date = NOW(), remark = null, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$newStatus, $mainStatus, $project_id]);
  } else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: admin_project_review.php");
    exit;
  }

  // Optional: notify staff
  $staffIdStmt = $pdo->prepare("SELECT assigned_to FROM projects WHERE id = ?");
  $staffIdStmt->execute([$project_id]);
  $staff_id = $staffIdStmt->fetchColumn();

  if ($staff_id) {
    $msg = $newStatus === 'confirmed fixed' ? "✅ Your project #$project_id has been confirmed." : "🔁 Project #$project_id needs a redo.";
    $notif = $pdo->prepare("INSERT INTO notifications (user_id, message, related_project_id, is_seen, created_at) VALUES (?, ?, ?, 0, NOW())");
    $notif->execute([$staff_id, $msg, $project_id]);
  }

  $_SESSION['success'] = "Project status updated.";
  header("Location: admin_project_review.php");
  exit;
}
