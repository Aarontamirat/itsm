<?php
require_once 'config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

if ($_FILES['profile_picture']['name']) {
  $user_id = $_SESSION['user_id'];
  $file_name = time() . '_' . basename($_FILES['profile_picture']['name']);
  $target = 'uploads/' . $file_name;

  if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target)) {
    $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
    $stmt->execute([$file_name, $user_id]);
    header("Location: profile.php?success=Profile picture updated");
    exit();
  } else {
    header("Location: profile.php?error=Upload failed");
    exit();
  }
}
