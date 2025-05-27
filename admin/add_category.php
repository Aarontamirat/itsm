<?php
include '../config/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['category_name']);
  if (!empty($name)) {
    try {
      $stmt = $pdo->prepare("INSERT INTO incident_categories (category_name) VALUES (?)");
      $stmt->execute([$name]);
      header('Location: categories.php?success=1');
    } catch (PDOException $e) {
      header('Location: categories.php?error=Duplicate entry');
    }
  } else {
    header('Location: categories.php?error=Empty field');
  }
}
?>
