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
<body class="flex bg-gray-100 min-h-screen">

    <?php include '../includes/sidebar.php'; ?>
    <div class="flex-1 ml-20">
    <?php include '../header.php'; ?>

    <div class="max-w-6xl mx-auto p-6">

        <!-- message -->
        <?php if (isset($_GET['success'])): ?>
        <div id='message' class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 animate-pulse">
            Action successful!
        </div>
        <?php elseif (isset($_GET['error'])): ?>
        <div id='message' class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 animate-pulse">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
        <?php elseif (isset($_GET['deleted'])): ?>
        <div id='message' class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-2 rounded mb-4 animate-pulse">
            Category deleted!
        </div>
        <?php endif; ?>


    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Incident Categories</h2>
        <button onclick="openAddModal()" class="bg-blue-600 text-white px-4 py-2 rounded">Add New</button>
    </div>

    <table class="min-w-full bg-white shadow-md rounded">
        <thead>
        <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Category Name</th>
            <th class="px-4 py-2">Created At</th>
            <th class="px-4 py-2">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($categories as $cat): ?>
            <tr class="border-t">
            <td class="px-4 py-2"><?= $cat['id'] ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($cat['name']) ?></td>
            <td class="px-4 py-2"><?= $cat['created_at'] ?></td>
            <td class="px-4 py-2">
                <button onclick="openEditModal(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>')" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                <button onclick="confirmDelete(<?= $cat['id'] ?>)" class="bg-red-600 text-white px-2 py-1 rounded">Delete</button>
            </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <!-- Add Category Modal -->
    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow-lg">
        <h3 class="text-lg font-semibold mb-4">Add New Category</h3>
        <form method="POST" action="add_category.php">
        <input type="text" name="name" placeholder="Category Name" class="border p-2 w-full mb-4" required>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add</button>
        <button type="button" onclick="closeAddModal()" class="ml-2 text-gray-600">Cancel</button>
        </form>
    </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow-lg">
        <h3 class="text-lg font-semibold mb-4">Edit Category</h3>
        <form method="POST" action="update_category.php">
        <input type="hidden" name="id" id="editId">
        <input type="text" name="name" id="editName" placeholder="Category Name" class="border p-2 w-full mb-4" required>
        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">Update</button>
        <button type="button" onclick="closeEditModal()" class="ml-2 text-gray-600">Cancel</button>
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

<!-- smooth timeout -->
 <script>
  setTimeout(() => {
    const msg = document.querySelector('#message');
    if (msg) msg.style.display = 'none';
  }, 3000);
</script>

</body>
</html>
