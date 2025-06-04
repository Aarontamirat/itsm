<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $pdo->prepare("SELECT u.id, u.name, u.email, u.role, u.profile_picture, b.name AS branch 
                       FROM users u LEFT JOIN branches b ON u.branch_id = b.id WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100 min-h-screen">

  <?php include '../includes/sidebar.php'; ?>
  <div class="flex-1 ml-20">


    <div class="max-w-4xl mx-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 pt-20 fade-in tech-border glow mt-8 font-mono">
        <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight">My Profile</h2>
        <p class="text-center text-cyan-500 mb-8">View and update your profile information</p>

        <div class="flex flex-col md:flex-row gap-10 items-start">
            <!-- Profile Sidebar -->
            <div class="flex flex-col items-center w-full md:w-1/3">
                <div class="relative">
                    <img src="<?= $user['profile_picture'] ? '../uploads/' . $user['profile_picture'] : '../uploads/default_avatar.png' ?>" alt="Profile Picture" class="w-36 h-36 rounded-full object-cover border-4 border-cyan-400 shadow-lg bg-cyan-50">
                    <form action="upload_profile_picture.php" method="POST" enctype="multipart/form-data" class="absolute bottom-0 right-0">
                        <label class="cursor-pointer bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 text-white px-3 py-1 rounded-full text-xs shadow hover:from-green-300 hover:to-cyan-400 font-semibold font-mono">
                            <input type="file" name="profile_picture" accept="image/*" class="hidden" onchange="this.form.submit()">
                            Change
                        </label>
                    </form>
                </div>
                <h2 class="mt-4 text-2xl font-bold text-cyan-800"><?= htmlspecialchars($user['name']) ?></h2>
                <p class="text-cyan-500"><?= htmlspecialchars($user['role']) ?></p>
                <p class="text-cyan-400 text-sm"><?= htmlspecialchars($user['branch']) ?></p>
            </div>

            <!-- Profile Details & Actions -->
            <div class="flex-1 w-full">
                <div class="mb-10">
                    <h3 class="text-xl font-semibold text-cyan-700 mb-4 border-b pb-2">Profile Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-cyan-600 font-medium">Full Name</p>
                            <p class="text-cyan-900 bg-cyan-50 rounded px-3 py-2 border border-cyan-100"><?= htmlspecialchars($user['name']) ?></p>
                        </div>
                        <div>
                            <p class="text-cyan-600 font-medium">Email</p>
                            <p class="text-cyan-900 bg-cyan-50 rounded px-3 py-2 border border-cyan-100"><?= htmlspecialchars($user['email']) ?></p>
                        </div>
                        <div>
                            <p class="text-cyan-600 font-medium">Role</p>
                            <p class="text-cyan-900 bg-cyan-50 rounded px-3 py-2 border border-cyan-100"><?= htmlspecialchars($user['role']) ?></p>
                        </div>
                        <div>
                            <p class="text-cyan-600 font-medium">Branch</p>
                            <p class="text-cyan-900 bg-cyan-50 rounded px-3 py-2 border border-cyan-100"><?= htmlspecialchars($user['branch']) ?></p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-xl font-semibold text-green-700 mb-4 border-b pb-2">Change Password</h3>
                    <?php 
                    if (isset($_GET['success'])): ?>
                        <div id="success-message" class="mb-4 text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-center font-semibold opacity-0 transition-opacity duration-500">
                            <?= htmlspecialchars($_GET['success']); unset($_GET['success']); ?>
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
                    <?php elseif (isset($_GET['error'])): ?>
                        <div id="error-message" class="mb-4 text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2 text-center font-semibold opacity-0 transition-opacity duration-500">
                            <?= htmlspecialchars($_GET['error']); unset($_GET['error']); ?>
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
                    <form action="change_password.php" method="POST" class="space-y-4">
                        <div>
                            <input type="password" name="current_password" placeholder="Current Password" class="w-full border border-cyan-200 bg-cyan-50 p-3 rounded-lg focus:ring-2 focus:ring-cyan-300 font-mono" required>
                        </div>
                        <div>
                            <input type="password" name="new_password" placeholder="New Password" class="w-full border border-cyan-200 bg-cyan-50 p-3 rounded-lg focus:ring-2 focus:ring-cyan-300 font-mono" required>
                        </div>
                        <div>
                            <input type="password" name="confirm_password" placeholder="Confirm New Password" class="w-full border border-cyan-200 bg-cyan-50 p-3 rounded-lg focus:ring-2 focus:ring-cyan-300 font-mono" required>
                        </div>
                        <button type="submit" class="w-full py-3 bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow transition font-mono tracking-widest">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>
</html>
