<?php
require '../config/db.php'; session_start();
if(!isset($_SESSION['user_id'],$_POST['comment'],$_POST['project_id'])) { die; }

$stmt = $pdo->prepare("INSERT INTO project_comments (project_id,user_id,comment) VALUES (?,?,?)");

if ($stmt->execute([intval($_POST['project_id']), $_SESSION['user_id'], trim($_POST['comment'])])) {
    // send notification to assigned_to in the projects table
    $stmt = $pdo->prepare("SELECT assigned_to FROM projects WHERE id=?");
    $stmt->execute([intval($_POST['project_id'])]);
    $assigned_to = $stmt->fetchColumn();
    if($assigned_to !== null && $assigned_to !== '') {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, related_project_id, is_seen, created_at) VALUES (?, ?, ?, 0, NOW())");
        $stmt->execute([$assigned_to, "A new comment has been added to a project (ID: ".intval($_POST['project_id']).")", intval($_POST['project_id'])]);
    }
}

header("Location: project_detail.php?id=".intval($_POST['project_id']));
