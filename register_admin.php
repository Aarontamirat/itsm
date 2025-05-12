<?php
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'admin'; // Fixed role

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([$name, $email, $password, $role]);
        $message = "Admin registered successfully. You can now delete this file.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Register Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 py-10">
    <div class="max-w-md mx-auto bg-white p-6 shadow rounded">
        <h2 class="text-xl font-bold mb-4">Register First Admin</h2>
        <?php if (isset($message)): ?>
        <div class="mb-4 text-green-600"><?= $message ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required
                class="block w-full mb-3 p-2 border rounded" />
            <input type="email" name="email" placeholder="Email" required
                class="block w-full mb-3 p-2 border rounded" />
            <input type="password" name="password" placeholder="Password" required
                class="block w-full mb-3 p-2 border rounded" />
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Register Admin</button>
        </form>
    </div>
</body>

</html>