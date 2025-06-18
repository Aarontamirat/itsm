<?php
// /C:/xampp/htdocs/itsmtest/reopen_incident.php

session_start();
header('Content-Type: application/json');

// Use PDO connection from config
require_once __DIR__ . '/config/db.php'; // adjust path if needed

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['incident_id'])) {
    echo json_encode(['success' => false, 'error' => 'No incident_id']);
    exit;
}
$incident_id = intval($data['incident_id']);

// Get logged-in user info
if (!isset($_SESSION['user_id'], $_SESSION['branch_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}
$user_id = intval($_SESSION['user_id']);
$branch_id = intval($_SESSION['branch_id']);

try {
    // 1. Set incident status to 'reopened'
    $stmt = $pdo->prepare("UPDATE incidents SET status = 'reopened', fixed_date = null, saved_amount = null, remark = null WHERE id = ?");
    $stmt->execute([$incident_id]);

    // 2. Get all admin users
    $admins = [];
    $stmt = $pdo->query("SELECT id FROM users WHERE role = 'admin'");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $admins[] = $row['id'];
    }

    // 3. Get all itstaff users assigned to the same branch
    $itstaff = [];
    $stmt = $pdo->prepare("
        SELECT u.id 
        FROM users u
        INNER JOIN staff_branch_assignments sba ON u.id = sba.staff_id
        WHERE u.role = 'staff' AND sba.branch_id = ?
    ");
    $stmt->execute([$branch_id]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $itstaff[] = $row['id'];
    }

    // 4. Insert notifications
    $all_users = array_unique(array_merge($admins, $itstaff));
    if (!empty($all_users)) {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, related_incident_id, created_at) VALUES (?, ?, ?, NOW())");
        $message = "Incident #$incident_id has been reopened.";
        foreach ($all_users as $notify_user_id) {
            $stmt->execute([$notify_user_id, $message, $incident_id]);
        }
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
