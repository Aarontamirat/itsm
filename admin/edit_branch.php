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

  <div class="max-w-xl mx-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 mt-10 fade-in tech-border glow">
    <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Edit Branch</h2>
    <p class="text-center text-cyan-500 mb-4 font-mono">Update branch details below</p>

    <?php if (isset($_SESSION['error'])): ?>
      <div id="error-message" class="mb-4 text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
      </div>
      <script>
        setTimeout(function() {
          var el = document.getElementById('error-message');
          if (el) el.style.opacity = '1';
        }, 10);
        setTimeout(function() {
          var el = document.getElementById('error-message');
          if (el) el.style.opacity = '0';
        }, 3010);
      </script>
    <?php endif; ?>

    <form action="handlers/update_branch.php" method="POST" class="space-y-5">
      <input type="hidden" name="id" value="<?= $branch['id'] ?>">
      <div>
        <label class="block text-cyan-700 font-semibold mb-1 font-mono" for="name">Branch Name</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($branch['name']) ?>" required
          class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono" />
      </div>
      <div>
        <label class="block text-cyan-700 font-semibold mb-1 font-mono" for="location">Location</label>
        <input type="text" name="location" id="location" value="<?= htmlspecialchars($branch['location']) ?>" required
          class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-green-200 focus:outline-none transition duration-200 font-mono" />
      </div>
      
      <div>
        <label class="block text-cyan-700 font-semibold mb-1 font-mono" for="phone">Phone Number</label>
        <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($branch['phone'] ?? '') ?>" required
          class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono" />
      </div>
      <div>
        <label class="block text-cyan-700 font-semibold mb-1 font-mono" for="email">Email</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($branch['email'] ?? '') ?>" required
          class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-green-200 focus:outline-none transition duration-200 font-mono" />
      </div>

      <div class="flex items-center">
        <input type="checkbox" name="is_active" id="is_active" value="1" class="h-5 w-5 text-cyan-600 border-cyan-300 rounded focus:ring-cyan-400" <?= $branch['is_active'] ? 'checked' : '' ?>>
        <label for="is_active" class="ml-2 text-cyan-700 font-semibold font-mono select-none">Active</label>
      </div>
      <button type="submit"
        class="w-full py-2 px-4 bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest">
        Update Branch
      </button>
    </form>
  </div>
</body>
</html>
