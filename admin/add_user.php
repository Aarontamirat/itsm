<?php
session_start();
require '../config/db.php';

// Only admins can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role  = $_POST['role'];
    $password = $_POST['password'];
    $branch_id = $_POST['branch_id'];

    // Basic validation
    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email is invalid.';
    if (empty($role) || !in_array($role, ['admin', 'it_staff', 'end_user'])) $errors[] = 'Invalid role selected.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';

    // If no errors, insert user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, branch_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $hashed_password, $role, $branch_id]);

        $_SESSION['success'] = "User created successfully.";
        header("Location: users.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <!-- header and sidebar -->
      <?php include '../includes/sidebar.php'; ?>
  <div class="flex-1 ml-20">
    <?php include '../header.php'; ?>

    <div class="max-w-md mx-auto bg-white p-6 mt-4 shadow rounded">
        <h2 class="text-xl font-bold mb-4">Add New User</h2>

        <?php if (!empty($errors)): ?>
        <div class="bg-red-100 text-red-800 p-3 mb-4 rounded">
            <ul class="list-disc ml-5">
                <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block">Name</label>
                <input type="text" name="name" class="w-full p-2 border rounded" required>
            </div>

            <div>
                <label class="block">Email</label>
                <input type="email" name="email" class="w-full p-2 border rounded" required>
            </div>

            <div>
            <label class="block" for="branch_id">Branch</label>
            <select name="branch_id" id="branch_id" required class="w-full border rounded p-2">
                <option value="">-- Select Branch --</option>
            <?php
                $branches = $pdo->query("SELECT id, name FROM branches")->fetchAll();
                foreach ($branches as $branch) {
                echo "<option value='{$branch['id']}'>{$branch['name']}</option>";
                }
            ?>
            </select>
            </div>

            <div>
                <label class="block">Role</label>
                <select name="role" class="w-full p-2 border rounded" required>
                    <option value="">-- Select Role --</option>
                    <option value="admin">Admin</option>
                    <option value="it_staff">IT Staff</option>
                    <option value="end_user">End User</option>
                </select>
            </div>

            <div>
                <label class="block">Password</label>
                <input type="password" name="password" class="w-full p-2 border rounded" required>
            </div>

            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add User</button>
                <a href="users.php" class="ml-2 text-gray-600">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>