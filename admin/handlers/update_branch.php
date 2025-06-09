<?php
require_once '../../config/db.php';
session_start();

$id = $_POST['id'] ?? null;
$name = trim($_POST['name'] ?? '');
$location = trim($_POST['location'] ?? '');
$is_active = isset($_POST['is_active']) ? 1 : 0;

if (!$id || !$name || !$location) {
    $_SESSION['error'] = "All fields are required.";
    header("Location: ../branches.php");
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE branches SET name = ?, location = ?, is_active = ? WHERE id = ?");
    $stmt->execute([$name, $location, $is_active, $id]);
    $_SESSION['success'] = "Branch updated successfully.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error updating branch: " . $e->getMessage();
}

header("Location: ../branches.php");
