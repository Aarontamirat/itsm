<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (strlen($newPassword) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    } elseif ($newPassword !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    } else {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, force_password_change = 0 WHERE id = ?");
        $stmt->execute([$hashed, $_SESSION['user_id']]);
        $success = "Password changed successfully. Redirecting...";
        header("refresh:2;url=" . ($_SESSION['role'] == 'staff' ? 'it_staff' : $_SESSION['role']) . "/" . ($_SESSION['role'] == 'staff' ? 'it_staff' : $_SESSION['role']) . "_dashboard.php");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Change Password</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
  <div class="bg-white p-6 rounded shadow-md w-full max-w-md">
    <h2 class="text-xl font-bold mb-4 text-center">Change Your Password</h2>

    <?php foreach ($errors as $error): ?>
      <div class="bg-red-100 text-red-700 p-2 rounded mb-2"><?= $error ?></div>
    <?php endforeach; ?>

    <?php if ($success): ?>
      <div class="bg-green-100 text-green-700 p-2 rounded mb-2"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
      <label class="block mb-2">New Password</label>
      <input type="password" name="new_password" class="w-full border rounded p-2 mb-4" required>

      <label class="block mb-2">Confirm New Password</label>
      <input type="password" name="confirm_password" class="w-full border rounded p-2 mb-4" required>

      <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Update Password</button>
    </form>
  </div>
</body>
</html>
