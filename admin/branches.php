<?php
session_start();
require_once '../config/db.php';

// Restrict to Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle feedback messages
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Fetch paginated branches
$limit = 5;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("SELECT * FROM branches ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$branches = $stmt->fetchAll();

// Count total for pagination
$total = $pdo->query("SELECT COUNT(*) FROM branches")->fetchColumn();
$totalPages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Branches</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<?php include '../includes/sidebar.php'; ?>
    <?php include '../header.php'; ?>

  <div class="bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 pt-8 fade-in tech-border glow md:max-w-4xl max-w-3xl mx-auto mt-8">
    <h1 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Branch Management</h1>
    <p class="text-center text-cyan-500 mb-1 font-mono">Manage your company branches</p>

    <?php if ($success): ?>
      <div id="success-message" class="mb-4 text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
        <?= htmlspecialchars($success) ?>
      </div>
      <script>
        setTimeout(function() {
          var el = document.getElementById('success-message');
          if (el) el.style.opacity = '1';
        }, 10);
        setTimeout(function() {
          var el = document.getElementById('success-message');
          if (el) el.style.opacity = '0';
        }, 3010);
      </script>
    <?php elseif ($error): ?>
      <div id="error-message" class="mb-4 text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
        <?= htmlspecialchars($error) ?>
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

    <form action="handlers/create_branch.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <div>
        <label class="block text-cyan-700 font-semibold mb-1 font-mono" for="name">Branch Name</label>
        <input type="text" name="name" id="name" placeholder="Branch Name" class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono" required>
      </div>
      <div>
        <label class="block text-cyan-700 font-semibold mb-1 font-mono" for="location">Location</label>
        <input type="text" name="location" id="location" placeholder="Location" class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-green-200 focus:outline-none transition duration-200 font-mono" required>
      </div>
      <div class="md:col-span-2">
        <button type="submit"
          class="w-full py-2 px-4 bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest">
          Add Branch
        </button>
      </div>
    </form>

    <div class="overflow-x-auto">
      <table class="w-full text-left border border-cyan-100 rounded-xl shadow font-mono">
        <thead class="bg-cyan-50">
          <tr>
            <th class="p-3">#</th>
            <th class="p-3">Name</th>
            <th class="p-3">Location</th>
            <th class="p-3">Created At</th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($branches as $branch): ?>
            <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition">
              <td class="p-3"><?= htmlspecialchars($branch['id']) ?></td>
              <td class="p-3"><?= htmlspecialchars($branch['name']) ?></td>
              <td class="p-3"><?= htmlspecialchars($branch['location']) ?></td>
              <td class="p-3"><?= htmlspecialchars($branch['created_at']) ?></td>
              <td class="p-3">
                <div class="flex gap-2">
                  <a href="edit_branch.php?id=<?= $branch['id'] ?>"
                    class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-lg font-semibold shadow transition">Edit</a>
                  <form action="handlers/delete_branch.php" method="POST" class="inline-block">
                    <input type="hidden" name="id" value="<?= $branch['id'] ?>">
                    <button type="submit"
                      class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg font-semibold shadow transition"
                      onclick="return confirm('Are you sure?')">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-center gap-2 font-mono">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>"
          class="px-4 py-2 rounded-lg border border-cyan-200 <?= $i == $page ? 'bg-cyan-400 text-white font-bold shadow' : 'bg-white text-cyan-700 hover:bg-cyan-50' ?> transition">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
  </div>
</body>
</html>
