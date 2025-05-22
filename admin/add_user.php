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
    $branch_id = $_POST['branch_id'];

    // Basic validation
    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email is invalid.';
    if (empty($role) || !in_array($role, ['admin', 'staff', 'user'])) $errors[] = 'Invalid role selected.';
    if (empty($branch_id)) $errors[] = 'Branch is required.';
    if (strlen($name) < 3) $errors[] = 'Name must be at least 3 characters long.';
    if (strlen($name) > 50) $errors[] = 'Name must not exceed 50 characters.';
    if (strlen($email) < 5) $errors[] = 'Email must be at least 5 characters long.';
    if (strlen($email) > 100) $errors[] = 'Email must not exceed 100 characters.';
    // if email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $errors[] = 'Email already exists.';
    }

    // If no errors, insert user
    if (empty($errors)) {
        $defaultPassword = 'pass@123'; // default password
        $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, branch_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $hashedPassword, $role, $branch_id]);

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
            <!-- name -->
            <div>
                <label class="block">Name</label>
                <input type="text" name="name" class="w-full p-2 border rounded" required>
            </div>

            <!-- email -->
            <div>
                <label class="block">Email</label>
                <input type="email" name="email" class="w-full p-2 border rounded" required>
            </div>

            <!-- branch -->
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

            <!-- role -->
            <div>
                <label class="block">Role</label>
                <select name="role" class="w-full p-2 border rounded" required>
                    <option value="">-- Select Role --</option>
                    <option value="admin">Admin</option>
                    <option value="staff">IT-Staff</option>
                    <option value="user">User</option>
                </select>
            </div>

            <!-- add button -->
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add User</button>
                <a href="users.php" class="ml-2 text-gray-600">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>