<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_SESSION['user_id'];
  $current = $_POST['current_password'];
  $new = $_POST['new_password'];
  $confirm = $_POST['confirm_password'];

  // Fetch current password hash
  $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
  $stmt->execute([$user_id]);
  $user = $stmt->fetch();

  if (password_verify($current, $user['password'])) {
    if ($new === $confirm) {
      $newHash = password_hash($new, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
      $stmt->execute([$newHash, $user_id]);
      header("Location: profile.php?success=Password changed");
    } else {
      header("Location: profile.php?error=Passwords do not match");
    }
  } else {
    header("Location: profile.php?error=Incorrect current password");
  }
  exit();
}
