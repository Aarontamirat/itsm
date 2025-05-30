<?php
include '../config/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];
  $name = trim($_POST['name']);
  if (!empty($name)) {
    try {
      $stmt = $pdo->prepare("UPDATE kb_categories SET name = ? WHERE id = ?");
      $stmt->execute([$name, $id]);
      header('Location: categories.php?success=1');
    } catch (PDOException $e) {
      header('Location: categories.php?error=Update failed');
    }
  }
}
?>
