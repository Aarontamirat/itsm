<?php
session_start();
require '../config/db.php';

// Restrict to Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Get staff and branches
$staff = $pdo->query("SELECT id, name FROM users WHERE role = 'staff' ORDER BY name ASC")->fetchAll();
$branches = $pdo->query("SELECT id, name FROM branches ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Assign IT Staff to Branches</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <?php include '../includes/sidebar.php'; ?>
    <?php include '../header.php'; ?>

    <div class="max-w-3xl mx-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 fade-in tech-border glow mt-8">
        <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Assign IT Staff to Branches</h2>
        <p class="text-center text-cyan-500 mb-6 font-mono">Assign one or more branches to an IT staff member</p>

        <div class="flex justify-end mb-6">
            <a href="staff_branch_assignments.php"
               class="inline-block bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
                View Staff-Branch Assignments
            </a>
        </div>

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

        <form action="save_staff_assignments.php" method="POST" class="space-y-8">
            <div>
                <label for="staff_id" class="block font-mono text-cyan-700 font-semibold mb-2">IT Staff:</label>
                <select name="staff_id" id="staff_id" required
                        class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono">
                    <option value="" disabled selected>Select staff...</option>
                    <?php foreach ($staff as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="branches" class="block font-mono text-cyan-700 font-semibold mb-2">Assign to Branch(es):</label>
                <select name="branch_ids[]" id="branches" multiple required
                        class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono h-40">
                    <?php foreach ($branches as $b): ?>
                        <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-cyan-400 mt-1 font-mono">Hold Ctrl (Windows) or Command (Mac) to select multiple branches.</p>
            </div>

            <div class="flex justify-center">
                <button type="submit"
                        class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-8 py-3 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
                    Save Assignments
                </button>
            </div>
        </form>
    </div>
</body>
</html>