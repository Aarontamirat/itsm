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

if (!$user) {
    die("User not found.");
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role  = $_POST['role'];

    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
    if (!in_array($role, ['admin', 'it_staff', 'end_user'])) $errors[] = 'Invalid role.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->execute([$name, $email, $role, $user_id]);

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

        <form method="POST" class="space-y-4">
            <div>
                <label class="block">Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>"
                    class="w-full p-2 border rounded" required>
            </div>

            <div>
                <label class="block">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
                    class="w-full p-2 border rounded" required>
            </div>

            <div>
                <label class="block">Role</label>
                <select name="role" class="w-full p-2 border rounded" required>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="it_staff" <?= $user['role'] === 'it_staff' ? 'selected' : '' ?>>IT Staff</option>
                    <option value="end_user" <?= $user['role'] === 'end_user' ? 'selected' : '' ?>>End User</option>
                </select>
            </div>

            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                <a href="users.php" class="ml-2 text-gray-600">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>