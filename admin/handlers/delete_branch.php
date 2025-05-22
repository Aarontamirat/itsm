<?php
require_once '../../config/db.php';
session_start();

$id = $_POST['id'] ?? null;

if (!$id || !is_numeric($id)) {
    $_SESSION['error'] = "Invalid branch ID.";
    header("Location: ../branches.php");
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM branches WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = "Branch deleted successfully.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error deleting branch: " . $e->getMessage();
}

header("Location: ../branches.php");
