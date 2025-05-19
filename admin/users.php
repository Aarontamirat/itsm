<?php
session_start();
require '../config/db.php';

// Only allow admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Pagination Setup
$results_per_page = 10;
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$total_users = $stmt->fetchColumn();
$total_pages = ceil($total_users / $results_per_page);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Fetch users
$stmt = $pdo->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT ?, ?");
$stmt->bindValue(1, $start_from, PDO::PARAM_INT);
$stmt->bindValue(2, $results_per_page, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>User Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<!-- header and sidebar -->
      <?php include '../includes/sidebar.php'; ?>
  <div class="flex-1 ml-20">
    <?php include '../header.php'; ?>

    <div class="max-w-5xl mx-auto bg-white p-6 mt-4 shadow rounded">
        <h2 class="text-2xl font-bold mb-4">User Management</h2>

        <!-- Add User Button -->
        <div class="mb-4">
            <a href="add_user.php" class="bg-blue-600 text-white px-4 py-2 rounded">+ Add New User</a>
        </div>

        <!-- User Table -->
        <table class="w-full border">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-2">#</th>
                    <th class="p-2">Name</th>
                    <th class="p-2">Email</th>
                    <th class="p-2">Role</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $index => $user): ?>
                    <tr class="border-t">
                        <td class="p-2"><?= $start_from + $index + 1 ?></td>
                        <td class="p-2"><?= htmlspecialchars($user['name']) ?></td>
                        <td class="p-2"><?= htmlspecialchars($user['email']) ?></td>
                        <td class="p-2 capitalize"><?= htmlspecialchars($user['role']) ?></td>
                        <td class="p-2">
                            <a href="edit_user.php?id=<?= $user['id'] ?>"
                                class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
                            <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Are you sure?')"
                                class="bg-red-600 text-white px-2 py-1 rounded ml-2">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-4">
            <nav class="flex justify-center">
                <ul class="flex space-x-2">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li>
                            <a href="?page=<?= $i ?>"
                                class="px-4 py-2 <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-gray-200' ?> rounded">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</body>

</html>