<?php
session_start();
require '../config/db.php';

// Restrict to Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch staff and their assigned branches
$stmt = $pdo->query("
    SELECT 
        u.id AS staff_id,
        u.name AS staff_name,
        u.email,
        u.profile_picture,
        b.id AS branch_id,
        b.name AS branch_name
    FROM users u
    LEFT JOIN staff_branch_assignments sba ON u.id = sba.staff_id
    LEFT JOIN branches b ON sba.branch_id = b.id
    WHERE u.role = 'staff'
    ORDER BY u.name ASC, b.name ASC
");

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group by staff
$staffs = [];
foreach ($rows as $row) {
    $staffId = $row['staff_id'];
    if (!isset($staffs[$staffId])) {
        $staffs[$staffId] = [
            'id' => $row['staff_id'],
            'name' => $row['staff_name'],
            'email' => $row['email'],
            'profile_picture' => $row['profile_picture'],
            'branches' => []
        ];
    }
    if ($row['branch_id']) {
        $staffs[$staffId]['branches'][] = $row['branch_name'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Branches per IT Staff</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .tech-border { border: 2px solid #22d3ee; }
        .glow { box-shadow: 0 0 16px 0 #22d3ee33; }
        .fade-in { animation: fadeIn 0.7s; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body class="bg-gray-100">

        <?php include '../includes/sidebar.php'; ?>
        <?php include '../header.php'; ?>

        <div class="max-w-7xl ms-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 fade-in tech-border glow mt-8">
                <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Branches Assigned per IT Staff</h2>
                <p class="text-center text-cyan-500 mb-6 font-mono">View and manage branch assignments for IT staff</p>

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

                <div class="overflow-x-auto rounded-xl shadow-inner">
                        <table class="w-full border border-cyan-100 bg-white bg-opacity-90 font-mono text-cyan-900">
                                <thead>
                                        <tr class="bg-cyan-50 text-cyan-700 text-left">
                                                <th class="p-3 font-bold">#</th>
                                                <th class="p-3 font-bold">Profile</th>
                                                <th class="p-3 font-bold">Name</th>
                                                <th class="p-3 font-bold">Email</th>
                                                <th class="p-3 font-bold">Assigned Branches</th>
                                                <th class="p-3 font-bold">Actions</th>
                                        </tr>
                                </thead>
                                <tbody>
                                        <?php $i = 1; foreach ($staffs as $staff): ?>
                                                <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition">
                                                        <td class="p-3"><?= $i++; ?></td>
                                                        <td class="p-3">
                                                                <?php if ($staff['profile_picture']): ?>
                                                                        <img src="../uploads/<?= htmlspecialchars($staff['profile_picture']) ?>" alt="Profile" class="w-12 h-12 rounded-full object-cover border">
                                                                <?php else: ?>
                                                                        <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold text-lg">
                                                                                <?= strtoupper($staff['name'][0]) ?>
                                                                        </div>
                                                                <?php endif; ?>
                                                        </td>
                                                        <td class="p-3 font-semibold text-cyan-700"><?= htmlspecialchars($staff['name']) ?></td>
                                                        <td class="p-3"><?= htmlspecialchars($staff['email']) ?></td>
                                                        <td class="p-3">
                                                                <?php if (count($staff['branches']) > 0): ?>
                                                                        <ul class="list-disc list-inside text-gray-700">
                                                                                <?php foreach ($staff['branches'] as $branch): ?>
                                                                                        <li><?= htmlspecialchars($branch) ?></li>
                                                                                <?php endforeach; ?>
                                                                        </ul>
                                                                <?php else: ?>
                                                                        <span class="italic text-gray-400">No branches assigned.</span>
                                                                <?php endif; ?>
                                                        </td>
                                                        <td class="p-3">
                                                                <?php if (count($staff['branches']) > 0): ?>
                                                                        <form method="POST" action="clear_assignments.php" onsubmit="return confirm('Are you sure you want to remove all branch assignments for this staff?');">
                                                                                <input type="hidden" name="staff_id" value="<?= $staff['id'] ?>">
                                                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded shadow font-mono">
                                                                                        Clear Assignments
                                                                                </button>
                                                                        </form>
                                                                <?php else: ?>
                                                                        <span class="text-gray-300">—</span>
                                                                <?php endif; ?>
                                                        </td>
                                                </tr>
                                        <?php endforeach; ?>
                                </tbody>
                        </table>
                </div>
        </div>
</body>
</html>
