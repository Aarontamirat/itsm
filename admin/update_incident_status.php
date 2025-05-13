<?php
require '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $incident_id = $_POST['incident_id'] ?? null;
  $new_status = $_POST['status'] ?? null;
  $updated_by = $_SESSION['user_id'] ?? null; // the admin performing the action

  if (!$incident_id || !$new_status || !$updated_by) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
  }

  // Update incident status
  $stmt = $pdo->prepare("UPDATE incidents SET status = ? WHERE id = ?");
  $stmt->bindParam(1, $new_status, PDO::PARAM_STR);
  $stmt->bindParam(2, $incident_id, PDO::PARAM_INT);

  if ($stmt->execute()) {
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
    echo json_encode(['success' => false, 'message' => 'Database update failed']);
    header("Location: incidents.php?success=Status Database update failed");
    exit;
  }
} else {
  echo json_encode(['success' => false, 'message' => 'Invalid request']);
  header("Location: incidents.php?success=Status Invalid Request");
    exit;
}

?>
