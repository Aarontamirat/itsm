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


// Fetch all branches
$branchFilter = $_GET['branch_id'] ?? '';
$branches = $pdo->query("SELECT id, name FROM branches")->fetchAll();

        

// Fetch users
if ($branchFilter) {
    $stmt = $pdo->prepare("SELECT u.*, b.name AS branch_name FROM users u
        LEFT JOIN branches b ON u.branch_id = b.id
        WHERE u.branch_id = ?");
    $stmt->execute([$branchFilter]);
    $users = $stmt->fetchAll();
} else {
$stmt = $pdo->prepare(
    "SELECT 
  users.id AS id,
  users.name AS name,
  users.email AS email,
  users.role AS role,
  users.created_at AS created_at,
  branches.id AS branch_id,
  branches.name AS branch_name
FROM 
  users
LEFT JOIN 
  branches ON users.branch_id = branches.id 
ORDER BY created_at DESC LIMIT ?, ?"
);
$stmt->bindValue(1, $start_from, PDO::PARAM_INT);
$stmt->bindValue(2, $results_per_page, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();
}
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

        <div class="flex justify-between items-center mb-4">
            <!-- Add User Button -->
            <div class="mb-4">
                <a href="add_user.php" class="bg-blue-600 text-white px-4 py-2 rounded">+ Add New User</a>
            </div>

            <div class="mb-4 flex flex-col items-center gap-2">
                <!-- Export Buttons -->
                 <div>
                    <a href="export_users_csv.php<?php if ($branchFilter) echo '?branch_id=' . $branchFilter; ?>" class="inline-block px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 mr-2">Export CSV</a>
                    <a href="export_users_pdf.php<?php if ($branchFilter) echo '?branch_id=' . $branchFilter; ?>" class="inline-block px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Export PDF</a>
                </div>

                <!-- Filter by Branch -->
                <form method="get" class="mb-4">
                    <label for="branchFilter" class="mr-2 font-medium text-gray-700">Filter by Branch:</label>
                    <select name="branch_id" id="branchFilter" class="px-3 py-1 border rounded">
                        <option value="">All Branches</option>
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>" <?= ($branchFilter == $branch['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($branch['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="ml-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Filter</button>
                </form>
            </div>
        </div>

        <!-- Password reset message -->
        <?php if (isset($_SESSION['success'])): ?>
        <div class="p-3 bg-green-100 text-green-800 rounded mb-4"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
        <div class="p-3 bg-red-100 text-red-800 rounded mb-4"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- User Table -->
        <table class="w-full border">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-2">#</th>
                    <th class="p-2">Name</th>
                    <th class="p-2">Email</th>
                    <th class="p-2">Branch</th>
                    <th class="p-2">Role</th>
                    <th class="p-2">Created At</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $index => $user): ?>
                    <tr class="border-t">
                        <td class="p-2"><?= $start_from + $index + 1 ?></td>
                        <td class="p-2"><?= htmlspecialchars($user['name']) ?></td>
                        <td class="p-2"><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['branch_name'] ?? 'N/A') ?></td>
                        <td class="p-2 capitalize"><?= htmlspecialchars($user['role']) ?></td>
                        <td class="p-2 capitalize"><?= htmlspecialchars($user['created_at']) ?></td>
                        <td class="p-2 flex space-x-2">
                            <a href="edit_user.php?id=<?= $user['id'] ?>"
                                class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
                            <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Are you sure?')"
                                class="bg-red-600 text-white px-2 py-1 rounded ml-2">Delete</a>

                            <!-- Reset password form -->
                            <form method="POST" action="reset_password.php" onsubmit="return confirm('Reset password for this user?');">
                                <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                                <button type="submit" class="bg-orange-500 text-white px-2 py-1 rounded">
                                Reset Password
                                </button>
                            </form>
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