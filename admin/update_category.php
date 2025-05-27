<?php
include '../config/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];
  $name = trim($_POST['category_name']);
  if (!empty($name)) {
    try {
      $stmt = $pdo->prepare("UPDATE incident_categories SET category_name = ? WHERE id = ?");
      $stmt->execute([$name, $id]);
      header('Location: categories.php?success=1');
    } catch (PDOException $e) {
      header('Location: categories.php?error=Update failed');
    }
  }
}
?>
