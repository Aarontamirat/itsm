<?php
require_once '../../config/db.php';
session_start();

$id = $_POST['id'] ?? null;
$name = trim($_POST['name'] ?? '');
$location = trim($_POST['location'] ?? '');

if (!$id || !$name || !$location) {
    $_SESSION['error'] = "All fields are required.";
    header("Location: ../branches.php");
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE branches SET name = ?, location = ? WHERE id = ?");
    $stmt->execute([$name, $location, $id]);
    $_SESSION['success'] = "Branch updated successfully.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error updating branch: " . $e->getMessage();
}

header("Location: ../branches.php");
