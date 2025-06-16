<?php
require '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $incidentId = $_POST['incident_id'] ?? null;
    $reason = trim($_POST['rejection_reason']) ?? null;

    // Fetch incident status by incident ID
    $stmt = $pdo->prepare("SELECT status, assigned_to FROM incidents WHERE id = ?");
    $stmt->execute([$incidentId]);
    $incidentStatus = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$incidentStatus) {
        echo json_encode(['success' => false, 'message' => 'Incident not found']);
        exit;
    }

    if (!$incidentId || !$reason) {
        echo json_encode(['success' => false, 'message' => 'Missing data']);
        exit;
    }
    if ($incidentStatus['status'] === 'assigned') {
        echo json_encode(['success' => false, 'message' => 'You can not reject an already assigned incident. Make sure the assignee declines the assignation first.']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE incidents SET status = 'rejected', rejected_by = ?, rejection_reason = ?, rejected_at = NOW() WHERE id = ?");
    if ($stmt->execute([$userId, $reason, $incidentId])) {
        // Fetch submitted_by
        $userStmt = $pdo->prepare("SELECT submitted_by FROM incidents WHERE id = ?");
        $userStmt->execute([$incidentId]);
        $incident = $userStmt->fetch();

        // Insert notification
        $notify = $pdo->prepare("INSERT INTO notifications (user_id, message, related_incident_id, is_seen, created_at)
                                VALUES (?, ?, ?, 0, NOW())");
        $notify->execute([
            $incident['submitted_by'],              // recipient (user)
            "Your incident has been rejected.",     // message
            $incidentId                             // related incident id
        ]);

        echo json_encode(['success' => true]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'DB error']);
    }
}

?>
