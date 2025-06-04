<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $incident_id = isset($_POST['incident_id']) ? intval($_POST['incident_id']) : 0;
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $user_id = $_SESSION['user_id'];

    if ($incident_id > 0 && $category_id > 0) {
        // Update the incident's category
        $stmt = $pdo->prepare("UPDATE incidents SET category_id = :category_id WHERE id = :incident_id");
        $stmt->execute([
            ':category_id' => $category_id,
            ':incident_id' => $incident_id
        ]);

        $catStmt = $pdo->prepare("SELECT name FROM kb_categories WHERE id = :category_id");
        $catStmt->execute([':category_id' => $category_id]);
        $category = $catStmt->fetch(PDO::FETCH_ASSOC);
        $category_name = $category ? $category['name'] : '';

        $log = $pdo->prepare("INSERT INTO incident_logs (incident_id, action, user_id, created_at) VALUES (?, ?, ?, NOW())");
        $log->execute([$incident_id, "Category changed to $category_name", $user_id]);

        // Add a success message to session
        $_SESSION['success'] = "Category updated successfully.";
    } else {
        $_SESSION['error'] = "Invalid input.";
    }

    // Redirect back to the previous page or incidents list
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// If accessed directly, redirect to incidents list
header("Location: my_incidents.php");
exit;
?>