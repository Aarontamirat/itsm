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
$branches = $pdo->query("SELECT id, name FROM branches")->fetchAll();

        

// Fetch users
// Filters
$branchFilter = $_GET['branch_id'] ?? '';
$roleFilter = $_GET['role'] ?? '';
$nameFilter = $_GET['name'] ?? '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Build WHERE clause and params
$where = [];
$params = [];

// Branch filter
if ($branchFilter !== '') {
    $where[] = 'u.branch_id = ?';
    $params[] = $branchFilter;
}

// Role filter
if ($roleFilter !== '') {
    $where[] = 'u.role = ?';
    $params[] = $roleFilter;
}

// Status filter (active/inactive)
if ($statusFilter !== '') {
    $where[] = 'u.is_active = ?';
    $params[] = $statusFilter;
}

// Name filter (partial match)
if ($nameFilter !== '') {
    $where[] = 'u.name LIKE ?';
    $params[] = '%' . $nameFilter . '%';
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Count total users for pagination (with filters)
$countSql = "SELECT COUNT(*) FROM users u $whereSql";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$total_users = $countStmt->fetchColumn();
$total_pages = ceil($total_users / $results_per_page);

// Fetch users with filters and pagination
$sql = "SELECT 
    u.id, u.name, u.email, u.role, u.is_active, u.created_at, u.job_position, 
    b.id AS branch_id, b.name AS branch_name
    FROM users u
    LEFT JOIN branches b ON u.branch_id = b.id
    $whereSql
    ORDER BY u.created_at DESC
    LIMIT ?, ?";
$stmt = $pdo->prepare($sql);

// Add pagination params
$execParams = array_merge($params, [$start_from, $results_per_page]);
foreach ($execParams as $k => $v) {
    // Last two params are integers for LIMIT
    if ($k >= count($params)) {
        $stmt->bindValue($k + 1, $v, PDO::PARAM_INT);
    } else {
        $stmt->bindValue($k + 1, $v);
    }
}
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

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-8">
            <a href="add_user.php"
               class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 transform hover:scale-105 transition duration-300 font-mono tracking-widest w-full md:w-auto text-center">
                + Add New User
            </a>
            <div class="flex flex-col md:flex-row md:items-center gap-4 w-full md:w-auto">
                <div class="flex gap-2 justify-center md:justify-start">
                    <a href="export_users_csv.php<?php
                        $params = [];
                        if ($branchFilter !== '') $params[] = 'branch_id=' . urlencode($branchFilter);
                        if ($roleFilter !== '') $params[] = 'role=' . urlencode($roleFilter);
                        if ($statusFilter !== '') $params[] = 'status=' . urlencode($statusFilter);
                        if ($nameFilter !== '') $params[] = 'name=' . urlencode($nameFilter);
                        if ($params) echo '?' . implode('&', $params);
                    ?>"
                       class="inline-block px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-mono font-semibold shadow transition text-center">
                        Export CSV
                    </a>
                    <a href="export_users_pdf.php<?php
                        $params = [];
                        if ($branchFilter !== '') $params[] = 'branch_id=' . urlencode($branchFilter);
                        if ($roleFilter !== '') $params[] = 'role=' . urlencode($roleFilter);
                        if ($statusFilter !== '') $params[] = 'status=' . urlencode($statusFilter);
                        if ($nameFilter !== '') $params[] = 'name=' . urlencode($nameFilter);
                        if ($params) echo '?' . implode('&', $params);
                    ?>"
                       class="inline-block px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-mono font-semibold shadow transition text-center">
                        Export PDF
                    </a>
                </div>
                <form method="get" class="w-full flex flex-col lg:flex-row flex-wrap items-stretch lg:items-center gap-2 lg:gap-4">
                    <!-- Branch Filter -->
                    <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-1 w-full lg:w-auto">
                        <label for="branchFilter" class="font-mono text-cyan-700 font-semibold lg:mr-2">Branch:</label>
                        <select name="branch_id" id="branchFilter"
                                class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono w-full lg:w-auto">
                            <option value="">All Branches</option>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?= $branch['id'] ?>" <?= ($branchFilter == $branch['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($branch['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Role Filter -->
                    <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-1 w-full lg:w-auto">
                        <label for="roleFilter" class="font-mono text-cyan-700 font-semibold lg:mr-2">Role:</label>
                        <select name="role" id="roleFilter"
                                class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono w-full lg:w-auto">
                            <option value="">All Roles</option>
                            <option value="admin" <?= ($roleFilter == 'admin') ? 'selected' : '' ?>>Admin</option>
                            <option value="staff" <?= ($roleFilter == 'staff') ? 'selected' : '' ?>>Staff</option>
                            <option value="user" <?= ($roleFilter == 'user') ? 'selected' : '' ?>>User</option>
                        </select>
                    </div>
                    <!-- Status Filter -->
                    <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-1 w-full lg:w-auto">
                        <label for="statusFilter" class="font-mono text-cyan-700 font-semibold lg:mr-2">Status:</label>
                        <select name="status" id="statusFilter"
                                class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono w-full lg:w-auto">
                            <option value="">All</option>
                            <option value="1" <?= (isset($_GET['status']) && $_GET['status'] === '1') ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?= (isset($_GET['status']) && $_GET['status'] === '0') ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <!-- Name Search -->
                    <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-1 w-full lg:w-auto">
                        <label for="nameFilter" class="font-mono text-cyan-700 font-semibold lg:mr-2">Name:</label>
                        <input type="text" name="name" id="nameFilter" value="<?= htmlspecialchars($nameFilter) ?>"
                               placeholder="Search name..."
                               class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono w-full lg:w-auto" />
                    </div>
                    <div class="flex items-center w-full lg:w-auto">
                        <button type="submit"
                                class="px-4 py-2 bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest w-full lg:w-auto">
                            Filter
                        </button>
                    </div>
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
                        <th class="p-3 font-bold">Job Position</th>
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

                            <td class="p-3 capitalize"><?= htmlspecialchars($user['job_position'] ?? '-') ?></td>
                            
                            <td class="p-3 capitalize">
                                <span class="<?= htmlspecialchars($user['is_active']) == 1 ? 'bg-green-400 text-white px-1 rounded-lg' : 'bg-red-400 text-white px-1 rounded-lg' ?>"><?= htmlspecialchars($user['is_active']) == 1 ? 'active' : 'inactive' ?></span>
                            </td>

                            <td class="p-3"><?= htmlspecialchars($user['created_at']) ?></td>

                            <td class="p-3 flex flex-col md:flex-row gap-2">
                                <a href="edit_user.php?id=<?= $user['id'] ?>"
                                    class="bg-yellow-400 hover:bg-yellow-500 text-white font-bold px-3 py-1 rounded-lg shadow transition">Edit</a>
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
                            <a href="?page=<?= $i ?>
                                <?= $branchFilter !== '' ? '&branch_id=' . urlencode($branchFilter) : '' ?>
                                <?= $roleFilter !== '' ? '&role=' . urlencode($roleFilter) : '' ?>
                                <?= $statusFilter !== '' ? '&status=' . urlencode($statusFilter) : '' ?>
                                <?= $nameFilter !== '' ? '&name=' . urlencode($nameFilter) : '' ?>"
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