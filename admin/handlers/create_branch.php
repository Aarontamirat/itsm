<?php
require_once '../../config/db.php';
session_start();

$name = trim($_POST['name'] ?? '');
$location = trim($_POST['location'] ?? '');
$is_active = isset($_POST['is_active']) ? 1 : 0;

if (!$name || !$location) {
    $_SESSION['error'] = "Name and Location are required.";
    header("Location: ../branches.php");
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO branches (name, location, is_active, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$name, $location, $is_active]);
    $_SESSION['success'] = "Branch created successfully.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error adding branch: " . $e->getMessage();
}

header("Location: ../branches.php");
