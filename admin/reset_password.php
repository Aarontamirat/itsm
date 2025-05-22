<?php
session_start();
require_once '../config/db.php';

// Check if admin is logged in and user_id is set
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin' || !isset($_POST['user_id'])) {
    header('Location: users.php');
    exit;
}

$userId = $_POST['user_id'];
$defaultPassword = 'pass@123'; // or generate random secure password
$hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE users SET password = :password, force_password_change = 1 WHERE id = :id");
    $stmt->execute([
        ':password' => $hashedPassword,
        ':id' => $userId
    ]);

    // Optional: log action
    $adminId = $_SESSION['user_id'];
    $log = $pdo->prepare("INSERT INTO incident_logs (user_id, action, created_at) VALUES (?, ?, NOW())");
    $log->execute([$adminId, "Reset password for user ID $userId"]);

    $_SESSION['success'] = "Password reset successfully.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error resetting password.";
}

header("Location: users.php");
exit;
