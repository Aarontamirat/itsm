<?php
session_start();
require '../config/db.php';

// Restrict to Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['staff_id'])) {
    $staffId = $_POST['staff_id'];

    $stmt = $pdo->prepare("DELETE FROM staff_branch_assignments WHERE staff_id = ?");
    if($stmt->execute([$staffId])) {
        $_SESSION['success'] = "Branch assignments cleared successfully.";
        header("Location: staff_branch_assignments.php");
        exit;
    } else {
        $_SESSION['error'] = "Branch assignments failed to cleared successfully.";
        header("Location: staff_branch_assignments.php");
        exit;
    }
}
