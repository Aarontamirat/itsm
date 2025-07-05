<?php
require '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    $id = intval($_POST['id']);

    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['success'] = "Project deleted successfully.";
    header("Location: projects.php");
    exit;
}
