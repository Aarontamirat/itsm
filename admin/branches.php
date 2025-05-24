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

  <div class="md:max-w-4xl max-w-3xl ml-20 md:mx-auto bg-white p-6 mt-4 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Branch Management</h1>

    <?php if ($success): ?>
      <div class="p-3 mb-4 text-green-700 bg-green-100 rounded"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="p-3 mb-4 text-red-700 bg-red-100 rounded"><?= $error ?></div>
    <?php endif; ?>

    <form action="handlers/create_branch.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
      <input type="text" name="name" placeholder="Branch Name" class="p-2 border rounded" required>
      <input type="text" name="location" placeholder="Location" class="p-2 border rounded" required>
      <button type="submit" class="md:col-span-2 w-1/5 bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Add Branch</button>
    </form>

    <table class="w-full text-left border">
      <thead class="bg-gray-200">
        <tr>
          <th class="p-2">#</th>
          <th class="p-2">Name</th>
          <th class="p-2">Location</th>
          <th class="p-2">Created At</th>
          <th class="p-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($branches as $branch): ?>
          <tr class="border-t">
            <td class="p-2"><?= htmlspecialchars($branch['id']) ?></td>
            <td class="p-2"><?= htmlspecialchars($branch['name']) ?></td>
            <td class="p-2"><?= htmlspecialchars($branch['location']) ?></td>
            <td class="p-2"><?= htmlspecialchars($branch['created_at']) ?></td>
            <td class="p-2">
              <div class="flex">
              <a href="edit_branch.php?id=<?= $branch['id'] ?>" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
              <form action="handlers/delete_branch.php" method="POST" class="inline-block">
                <input type="hidden" name="id" value="<?= $branch['id'] ?>">
                <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded ml-2" onclick="return confirm('Are you sure?')">Delete</button>
              </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" class="px-3 py-1 rounded border <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-white text-blue-600' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>
</body>
</html>
