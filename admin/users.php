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
  users.is_active AS is_active,
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
    <?php include '../header.php'; ?>

    <div class="max-w-7xl ms-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 fade-in tech-border glow mt-8">
        <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">User Management</h2>
        <p class="text-center text-cyan-500 mb-1 font-mono">Manage IT Support user accounts</p>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div id="success-message" class="mb-4 text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
                <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
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
        <?php endif; ?>
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

        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <a href="add_user.php" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
                + Add New User
            </a>
            <div class="flex flex-col md:flex-row items-center gap-4">
                <div>
                    <a href="export_users_csv.php<?php if ($branchFilter) echo '?branch_id=' . $branchFilter; ?>" class="inline-block px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-mono font-semibold shadow transition">Export CSV</a>
                    <a href="export_users_pdf.php<?php if ($branchFilter) echo '?branch_id=' . $branchFilter; ?>" class="inline-block px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-mono font-semibold shadow transition ml-2">Export PDF</a>
                </div>
                <form method="get" class="flex items-center gap-2">
                    <label for="branchFilter" class="font-mono text-cyan-700 font-semibold">Branch:</label>
                    <select name="branch_id" id="branchFilter" class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono">
                        <option value="">All Branches</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= $branch['id'] ?>" <?= ($branchFilter == $branch['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($branch['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest">
                        Filter
                    </button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl shadow-inner">
            <table class="w-full border border-cyan-100 bg-white bg-opacity-90 font-mono text-cyan-900">
                <thead>
                    <tr class="bg-cyan-50 text-cyan-700 text-left">
                        <th class="p-3 font-bold">#</th>
                        <th class="p-3 font-bold">Name</th>
                        <th class="p-3 font-bold">Email</th>
                        <th class="p-3 font-bold">Branch</th>
                        <th class="p-3 font-bold">Role</th>
                        <th class="p-3 font-bold">Status</th>
                        <th class="p-3 font-bold">Created At</th>
                        <th class="p-3 font-bold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $index => $user): ?>
                        <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition">
                            <td class="p-3"><?= $start_from + $index + 1 ?></td>
                            <td class="p-3"><?= htmlspecialchars($user['name']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($user['branch_name'] ?? 'N/A') ?></td>
                            <td class="p-3 capitalize"><?= htmlspecialchars($user['role']) ?></td>

                            <?php
                            if($user['is_active'] == 1) {
                                $statusClass = 'p-3 capitalize bg-green rounded-lg';
                                $statusText = 'Active';
                                echo '<td class="p-3 capitalize bg-green rounded-lg">Active</td>';
                            } elseif($user['is_active'] == 0) {
                                $statusClass = 'p-3 capitalize bg-red text-white animation-pulse rounded-lg';
                                $statusText = 'Inactive';
                                echo '<td class="' . $statusClass . '">' .$statusText . '</td>';
                            }
                            ?>

                            <td class="p-3"><?= htmlspecialchars($user['created_at']) ?></td>

                            <td class="p-3 flex flex-col md:flex-row gap-2">
                                <a href="edit_user.php?id=<?= $user['id'] ?>"
                                    class="bg-yellow-400 hover:bg-yellow-500 text-white font-bold px-3 py-1 rounded-lg shadow transition">Edit</a>
                                <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Are you sure?')"
                                    class="bg-red-500 hover:bg-red-600 text-white font-bold px-3 py-1 rounded-lg shadow transition">Delete</a>
                                <form method="POST" action="reset_password.php" onsubmit="return confirm('Reset password for this user?');" class="inline">
                                    <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                                    <button type="submit" class="bg-orange-400 hover:bg-orange-500 text-white font-bold px-3 py-1 rounded-lg shadow transition">
                                        Reset Password
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            <nav class="flex justify-center">
                <ul class="flex space-x-2 font-mono">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li>
                            <a href="?page=<?= $i ?><?= $branchFilter ? '&branch_id=' . $branchFilter : '' ?>"
                                class="px-4 py-2 <?= $i == $page ? 'bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 text-white font-bold' : 'bg-cyan-50 text-cyan-700' ?> rounded-lg shadow transition">
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