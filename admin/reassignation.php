<?php
session_start();
require_once '../config/db.php';

// Restrict to Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch the incident ID from the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['incident_id'])) {
    $incident_id = (int)$_POST['incident_id'];

    // Fetch the incident details
    $stmt = $pdo->prepare("SELECT * FROM incidents WHERE id = ?");
    $stmt->execute([$incident_id]);
    $incident = $stmt->fetch();

    // Fetch all IT Staff
    $staff_stmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'staff'");
    $staff_stmt->execute();
    $it_staff = $staff_stmt->fetchAll();

    // Handle reassignment
    if (isset($_POST['staff_id'])) {
        $staff_id = (int)$_POST['staff_id'];
        
        $status = 'assigned';
        // Update the incident with the new staff ID
        $update_stmt = $pdo->prepare("UPDATE incidents SET status = ?, assigned_to = ? WHERE id = ?");
        if ($update_stmt->execute([$status, $staff_id, $incident_id])) {
            $_SESSION['success'] = "Incident reassigned successfully.";
            exit;
        } else {
            $_SESSION['error'] = "Failed to reassign incident.";
        }
    }
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: incidents.php");
    exit;
}
?>