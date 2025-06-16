<?php
require '../config/db.php';

$staffId = $_POST['staff_id'];
$branchIds = $_POST['branch_ids'];

$pdo->beginTransaction();
session_start();
try {
    // Remove old assignments
    $pdo->prepare("DELETE FROM staff_branch_assignments WHERE staff_id = ?")->execute([$staffId]);

    // Insert new ones
    $stmt = $pdo->prepare("INSERT INTO staff_branch_assignments (staff_id, branch_id) VALUES (?, ?)");
    foreach ($branchIds as $branchId) {
        $stmt->execute([$staffId, $branchId]);
    }

    $pdo->commit();
    $_SESSION['success'] = "Assignments updated successfully!";
    header('Location: assign_staff_branches.php');
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header('Location: assign_staff_branches.php');
}
?>
