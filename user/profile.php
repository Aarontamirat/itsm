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


    <div class="max-w-3xl mx-auto my-12 p-8 bg-white rounded-2xl shadow-2xl flex flex-col md:flex-row gap-10 items-start">
        <!-- Profile Sidebar -->
        <div class="flex flex-col items-center w-full md:w-1/3">
            <div class="relative">
                <img src="<?= $user['profile_picture'] ? '../uploads/' . $user['profile_picture'] : '../uploads/default_avatar.png' ?>" alt="Profile Picture" class="w-32 h-32 rounded-full object-cover border-4 border-blue-400 shadow-lg">
                <form action="upload_profile_picture.php" method="POST" enctype="multipart/form-data" class="absolute bottom-0 right-0">
                    <label class="cursor-pointer bg-blue-600 text-white px-2 py-1 rounded-full text-xs shadow hover:bg-blue-700">
                        <input type="file" name="profile_picture" accept="image/*" class="hidden" onchange="this.form.submit()">
                        Change
                    </label>
                </form>
            </div>
            <h2 class="mt-4 text-2xl font-bold text-gray-800"><?= htmlspecialchars($user['name']) ?></h2>
            <p class="text-gray-500"><?= htmlspecialchars($user['role']) ?></p>
            <p class="text-gray-400 text-sm"><?= htmlspecialchars($user['branch']) ?></p>
        </div>

        <!-- Profile Details & Actions -->
        <div class="flex-1 w-full">
            <div class="mb-8">
                <h3 class="text-xl font-semibold text-blue-700 mb-4 border-b pb-2">Profile Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600 font-medium">Full Name</p>
                        <p class="text-gray-900 bg-gray-100 rounded px-3 py-2"><?= htmlspecialchars($user['name']) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Email</p>
                        <p class="text-gray-900 bg-gray-100 rounded px-3 py-2"><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Role</p>
                        <p class="text-gray-900 bg-gray-100 rounded px-3 py-2"><?= htmlspecialchars($user['role']) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Branch</p>
                        <p class="text-gray-900 bg-gray-100 rounded px-3 py-2"><?= htmlspecialchars($user['branch']) ?></p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-xl font-semibold text-green-700 mb-4 border-b pb-2">Change Password</h3>
                <?php 
                // display success or error messages if GET parameters are set
                if (isset($_GET['success'])) {
                    echo '<div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800">' . $_GET['success'] . '</div>';
                    unset($_GET['success']);
                } elseif (isset($_GET['error'])) {
                    echo '<div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800">' . $_GET['error'] . '</div>';
                    unset($_GET['error']);
                }
                ?>
                <form action="change_password.php" method="POST" class="space-y-4">
                    <div>
                        <input type="password" name="current_password" placeholder="Current Password" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-200" required>
                    </div>
                    <div>
                        <input type="password" name="new_password" placeholder="New Password" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-200" required>
                    </div>
                    <div>
                        <input type="password" name="confirm_password" placeholder="Confirm New Password" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-200" required>
                    </div>
                    <button type="submit" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow transition">Change Password</button>
                </form>
            </div>
        </div>
    </div>
    </div>
</body>
</html>
