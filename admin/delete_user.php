<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$user_id = (int)$_GET['id'];

// Prevent admin from deleting their own account
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['error'] = "You cannot delete your own account.";
    header("Location: users.php");
    exit;
}

// Check if user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "User not found.";
    header("Location: users.php");
    exit;
}

// Delete user
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$user_id]);

$_SESSION['success'] = "User deleted successfully.";
header("Location: users.php");
exit;
