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
    $is_active = isset($_POST['is_active']) ? 1 : 0;

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
    if (!is_numeric($is_active)) $errors[] = 'Invalid active status.';

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

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, branch_id, created_at, is_active) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
        $stmt->execute([$name, $email, $hashedPassword, $role, $branch_id, $is_active]);

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

    <div class="bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 pt-20 fade-in tech-border glow max-w-lg mx-auto mt-8">
        <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Add New User</h2>
        <p class="text-center text-cyan-500 mb-1 font-mono">Create a new IT Support user account</p>
        <p class="text-center text-green-500 mb-6 font-mono text-xs">Property of Lucy Insurance</p>

        <?php if (!empty($errors)): ?>
            <div id="error-message" class="mb-4 text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
                <ul class="list-disc ml-5 inline-block text-left">
                    <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
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

        <form method="POST" class="space-y-5">
            <!-- name -->
            <div>
                <label class="block text-cyan-700 font-semibold mb-1 font-mono" for="name">Name</label>
                <input type="text" name="name" id="name" required
                    class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono"
                    value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
            </div>

            <!-- email -->
            <div>
                <label class="block text-cyan-700 font-semibold mb-1 font-mono" for="email">Email</label>
                <input type="email" name="email" id="email" required
                    class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono"
                    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>

            <!-- branch -->
            <div>
                <label class="block text-cyan-700 font-semibold mb-1 font-mono" for="branch_id">Branch</label>
                <select name="branch_id" id="branch_id" required
                    class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-green-200 focus:outline-none transition duration-200 font-mono">
                    <option value="">-- Select Branch --</option>
                    <?php
                        $branches = $pdo->query("SELECT id, name FROM branches")->fetchAll();
                        foreach ($branches as $branch) {
                            $selected = (isset($_POST['branch_id']) && $_POST['branch_id'] == $branch['id']) ? 'selected' : '';
                            echo "<option value='{$branch['id']}' $selected>{$branch['name']}</option>";
                        }
                    ?>
                </select>
            </div>

            <!-- role -->
            <div>
                <label class="block text-cyan-700 font-semibold mb-1 font-mono" for="role">Role</label>
                <select name="role" id="role" required
                    class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-green-200 focus:outline-none transition duration-200 font-mono">
                    <option value="">-- Select Role --</option>
                    <option value="admin" <?= (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                    <option value="staff" <?= (isset($_POST['role']) && $_POST['role'] == 'staff') ? 'selected' : '' ?>>IT-Staff</option>
                    <option value="user" <?= (isset($_POST['role']) && $_POST['role'] == 'user') ? 'selected' : '' ?>>User</option>
                </select>
            </div>

            <!-- is_active checkbox -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    class="h-4 w-4 text-cyan-600 border-gray-300 rounded focus:ring-cyan-500"
                    <?= (!isset($_POST['is_active']) || $_POST['is_active'] == '1') ? 'checked' : 'unchecked' ?>>
                <label for="is_active" class="ml-2 block text-cyan-700 font-semibold font-mono">
                    Active
                </label>
            </div>

            <!-- add button -->
            <div>
                <button type="submit"
                    class="w-full py-2 px-4 bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest">
                    Add User
                </button>
                <a href="users.php" class="block mt-3 text-center text-green-500 hover:underline text-sm transition font-mono">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>