<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../config/db.php'; // DB connection

// Fetch categories
$stmt = $pdo->query("SELECT * FROM kb_categories ORDER BY id DESC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Categories</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <?php include '../includes/sidebar.php'; ?>
    <?php include '../header.php'; ?>

    <div class="max-w-5xl mx-auto p-8">
      <div class="bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 pt-8 fade-in tech-border glow">
        <!-- message -->
        <?php if (isset($_GET['success'])): ?>
        <div id='message' class="mb-4 text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
          Action successful!
        </div>
        <?php elseif (isset($_GET['error'])): ?>
        <div id='message' class="mb-4 text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
          <?= htmlspecialchars($_GET['error']) ?>
        </div>
        <?php elseif (isset($_GET['deleted'])): ?>
        <div id='message' class="mb-4 text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
          Category deleted!
        </div>
        <?php endif; ?>

        <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Incident Categories</h2>
        <p class="text-center text-cyan-500 mb-6 font-mono">Manage your knowledge base categories</p>

        <div class="flex justify-end mb-6">
          <button onclick="openAddModal()" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold px-6 py-2 rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest">
            Add New
          </button>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full bg-white bg-opacity-90 shadow rounded-2xl font-mono">
            <thead>
            <tr>
              <th class="px-4 py-3 text-cyan-700 font-semibold text-left">ID</th>
              <th class="px-4 py-3 text-cyan-700 font-semibold text-left">Category Name</th>
              <th class="px-4 py-3 text-cyan-700 font-semibold text-left">Created At</th>
              <th class="px-4 py-3 text-cyan-700 font-semibold text-left">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($categories as $cat): ?>
              <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition">
                <td class="px-4 py-2"><?= $cat['id'] ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($cat['name']) ?></td>
                <td class="px-4 py-2"><?= $cat['created_at'] ?></td>
                <td class="px-4 py-2 space-x-2">
                  <button onclick="openEditModal(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>')" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-lg font-semibold shadow transition font-mono">Edit</button>
                  <button onclick="confirmDelete(<?= $cat['id'] ?>)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg font-semibold shadow transition font-mono">Delete</button>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Add Category Modal -->
      <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-8 fade-in tech-border glow w-full max-w-md">
          <h3 class="text-2xl font-bold text-cyan-700 mb-4 text-center font-mono">Add New Category</h3>
          <form method="POST" action="add_category.php" class="space-y-5">
            <input type="text" name="name" placeholder="Category Name"
              class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono"
              required>
            <div class="flex justify-center space-x-4">
              <button type="submit"
                class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold px-6 py-2 rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest">
                Add
              </button>
              <button type="button" onclick="closeAddModal()"
                class="px-6 py-2 rounded-lg border border-cyan-200 text-cyan-700 bg-cyan-50 hover:bg-cyan-100 font-mono font-semibold transition">
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Edit Category Modal -->
      <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-8 fade-in tech-border glow w-full max-w-md">
          <h3 class="text-2xl font-bold text-cyan-700 mb-4 text-center font-mono">Edit Category</h3>
          <form method="POST" action="update_category.php" class="space-y-5">
            <input type="hidden" name="id" id="editId">
            <input type="text" name="name" id="editName" placeholder="Category Name"
              class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-green-200 focus:outline-none transition duration-200 font-mono"
              required>
            <div class="flex justify-center space-x-4">
              <button type="submit"
                class="bg-yellow-400 hover:bg-yellow-500 text-white font-bold px-6 py-2 rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest">
                Update
              </button>
              <button type="button" onclick="closeEditModal()"
                class="px-6 py-2 rounded-lg border border-cyan-200 text-cyan-700 bg-cyan-50 hover:bg-cyan-100 font-mono font-semibold transition">
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- modal toggle -->
    <script>
      function openAddModal() {
      document.getElementById('addModal').classList.remove('hidden');
      }
      function closeAddModal() {
      document.getElementById('addModal').classList.add('hidden');
      }
      function openEditModal(id, name) {
      document.getElementById('editId').value = id;
      document.getElementById('editName').value = name;
      document.getElementById('editModal').classList.remove('hidden');
      }
      function closeEditModal() {
      document.getElementById('editModal').classList.add('hidden');
      }
      function confirmDelete(id) {
      if(confirm('Are you sure you want to delete this category?')) {
        window.location.href = 'delete_category.php?id=' + id;
      }
      }
    </script>

    <!-- smooth fade for message -->
    <script>
      setTimeout(function() {
        var el = document.getElementById('message');
        if (el) el.style.opacity = '1';
      }, 10);
      setTimeout(function() {
        var el = document.getElementById('message');
        if (el) el.style.opacity = '0';
      }, 3010);
    </script>

</body>
</html>
