<?php
// /C:/xampp/htdocs/itsmtest/confirm_fixed.php

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['incident_id']) || !is_numeric($input['incident_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid incident ID']);
    exit;
}

$incident_id = intval($input['incident_id']);

// Use existing PDO connection from ./config/db.php
require_once __DIR__ . '/config/db.php'; // Assumes $pdo is defined in this file

if (!isset($pdo) || !$pdo instanceof PDO) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection not available']);
    exit;
}

// Update incident status to "fixed_confirmed"
try {
    $stmt = $pdo->prepare("UPDATE incidents SET status = :status WHERE id = :id");
    $status = 'fixed_confirmed'; // Change as per your status naming
    $stmt->execute([':status' => $status, ':id' => $incident_id]);

    // notify IT staff
    // Get the assigned_to user (IT staff) for this incident
    $stmt = $pdo->prepare("SELECT assigned_to FROM incidents WHERE id = :id");
    $stmt->execute([':id' => $incident_id]);
    $assigned = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($assigned && !empty($assigned['assigned_to'])) {
        $itstaff_id = $assigned['assigned_to'];
        // Here you would notify the IT staff, e.g., insert a notification or send an email
        // Example: insert into notifications table
        $notify_stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, related_incident_id, created_at) VALUES (:user_id, :message, :incident_id, NOW())");
        $message = "Incident #$incident_id has been confirmed as fixed.";
        $notify_stmt->execute([
            ':user_id' => $itstaff_id,
            ':message' => $message,
            ':incident_id' => $incident_id
        ]);
    }
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to update incident']);
}
