<?php
require_once '../config/db.php';
session_start();

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) die('Invalid ID');

$stmt = $pdo->prepare("SELECT * FROM branches WHERE id = ?");
$stmt->execute([$id]);
$branch = $stmt->fetch();
if (!$branch) die('Branch not found');
?>

<!DOCTYPE html>
<html>
<head><title>Edit Branch</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-100">

<?php include '../includes/sidebar.php'; ?>
    <?php include '../header.php'; ?>

  <div class="max-w-xl mx-auto bg-white p-6 mt-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Edit Branch</h2>

    <form action="handlers/update_branch.php" method="POST" class="grid gap-4">
      <input type="hidden" name="id" value="<?= $branch['id'] ?>">
      <input type="text" name="name" value="<?= htmlspecialchars($branch['name']) ?>" class="p-2 border rounded" required>
      <input type="text" name="location" value="<?= htmlspecialchars($branch['location']) ?>" class="p-2 border rounded" required>
      <button type="submit" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Update</button>
    </form>
  </div>
</body>
</html>
