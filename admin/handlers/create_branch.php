<?php
require_once '../../config/db.php';
session_start();

$name = trim($_POST['name'] ?? '');
$location = trim($_POST['location'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$is_active = isset($_POST['is_active']) ? 1 : 0;

if (!$name || !$location || !$phone || !$email) {
    $_SESSION['error'] = "Name, Location, Phone Number, and Email are required.";
    header("Location: ../branches.php");
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO branches (name, location, phone, email, is_active, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $location, $phone, $email, $is_active]);
    $_SESSION['success'] = "Branch created successfully.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error adding branch: " . $e->getMessage();
}

header("Location: ../branches.php");
