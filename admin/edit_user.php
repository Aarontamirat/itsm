<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$user_id = (int)$_GET['id'];

// Fetch user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch allusers
$stmt2 = $pdo->prepare("SELECT * FROM users");
$stmt2->execute([]);
$users = $stmt2->fetchAll();

// Fetch user per role
$stmt3 = $pdo->prepare("SELECT * FROM users WHERE role = 'admin'");
$stmt3->execute([]);
$admin_users = $stmt3->fetchAll();


if (!$user) {
    die("User not found.");
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $branch_id = $_POST['branch_id'];
    $role  = $_POST['role'];

    // Validate inputs
    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
    if (!in_array($role, ['admin', 'staff', 'user'])) $errors[] = 'Invalid role.';
    if (empty($branch_id)) $errors[] = 'Branch is required.';
    if (strlen($name) < 3) $errors[] = 'Name must be at least 3 characters long.';
    if (strlen($name) > 50) $errors[] = 'Name must not exceed 50 characters.';
    if (strlen($email) < 5) $errors[] = 'Email must be at least 5 characters long.';
    if (strlen($email) > 100) $errors[] = 'Email must not exceed 100 characters.';
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->rowCount() > 0) {
        $errors[] = 'Email already exists.';
    }

    if (count($admin_users) <= 1 && $role !== 'admin' && $user['role'] == 'admin') $errors[] = 'At least one admin user is required.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, branch_id = ?, role = ? WHERE id = ?");
        $stmt->execute([$name, $email, $branch_id, $role, $user_id]);

        $_SESSION['success'] = "User updated successfully.";
        header("Location: users.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-md mx-auto bg-white p-6 shadow rounded">
        <h2 class="text-xl font-bold mb-4">Edit User</h2>

        <?php if (!empty($errors)): ?>
        <div class="bg-red-100 text-red-800 p-3 mb-4 rounded">
            <ul class="list-disc ml-5">
                <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
<p><?php echo count($admin_users); ?></p>
        <form method="POST" class="space-y-4">

            <!-- name -->
            <div>
                <label class="block">Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>"
                    class="w-full p-2 border rounded" required>
            </div>

            <!-- email -->
            <div>
                <label class="block">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
                    class="w-full p-2 border rounded" required>
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
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="staff" <?= $user['role'] === 'staff' ? 'selected' : '' ?>>IT Staff</option>
                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>End User</option>
                </select>
            </div>

            <!-- update button -->
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                <a href="users.php" class="ml-2 text-gray-600">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>