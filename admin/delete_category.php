<?php
include '../config/db.php';
$id = $_GET['id'] ?? null;
if ($id) {
  $stmt = $pdo->prepare("DELETE FROM kb_categories WHERE id = ?");
  $stmt->execute([$id]);
  header('Location: categories.php?deleted=1');
}
?>
