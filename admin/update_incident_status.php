<?php
require '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $incident_id = $_POST['incident_id'] ?? null;
  $new_status = $_POST['status'] ?? null;
  $updated_by = $_SESSION['user_id'] ?? null; // the admin performing the action

  if (!$incident_id || !$new_status || !$updated_by) {
    echo json_encode(['error' => true, 'message' => 'Missing data']);
    exit;
  }

  // Update incident status

// if the status is assigned and the status in the database is assigned, let it be updated, if not show an error to assign the incident to an IT staff
  $stmt = $pdo->prepare("SELECT status FROM incidents WHERE id = ?");
  $stmt->execute([$incident_id]);
  $currentIncident = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($currentIncident && $new_status === 'assigned' && $currentIncident['status'] !== 'assigned') {
    echo json_encode(['error' => true, 'message' => 'Database update failed']);
    header("Location: incidents.php?error=Assign an IT staff before selecting this option");
    exit;
  }   
  
  $stmt = $pdo->prepare("UPDATE incidents SET status = ? WHERE id = ?");
  $stmt->bindParam(1, $new_status, PDO::PARAM_STR);
  $stmt->bindParam(2, $incident_id, PDO::PARAM_INT);

  if ($stmt->execute()) {
    // Fetch the user who created the incident
            $stmtUser = $pdo->prepare("SELECT submitted_by FROM incidents WHERE id = ?");
            $stmtUser->execute([$incident_id]);
            $incidentUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if ($incidentUser) {
                $userId = $incidentUser['submitted_by'];
                $message = "Your incident (ID: $incident_id) has been marked as $new_status.";

                // Insert into notifications
                $stmtNotif = $pdo->prepare("INSERT INTO notifications (user_id, message, related_incident_id, is_seen, created_at) VALUES (?, ?, ?, 0, NOW())");
                $stmtNotif->execute([$userId, $message, $incident_id]);
            }

    // Insert into incident_logs
    $log_stmt = $pdo->prepare("INSERT INTO incident_logs (incident_id, user_id, action) VALUES (?, ?, ?)");
    $action = "Status updated to '$new_status'";
    $log_stmt->bindParam(1, $incident_id, PDO::PARAM_STR);
    $log_stmt->bindParam(2, $updated_by, PDO::PARAM_STR);
    $log_stmt->bindParam(3, $action, PDO::PARAM_STR);
    $log_stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Status updated']);
    header("Location: incidents.php?success=Status updated successfully");
    exit;
  } else {
    echo json_encode(['error' => true, 'message' => 'Database update failed']);
    header("Location: incidents.php?error=Status Database update failed");
    exit;
  }
} else {
  echo json_encode(['error' => true, 'message' => 'Invalid request']);
  header("Location: incidents.php?error=Status Invalid Request");
    exit;
}

?>
