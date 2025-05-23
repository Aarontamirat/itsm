<?php
session_start();
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // $stmt = $pdo->prepare(
    //     "SELECT * FROM users WHERE email = ?

    //     "
    // );

    //fetch all users data and branch name by joining users and branches table
    $stmt = $pdo->prepare(
        "SELECT u.*, 
        b.name AS branch_name
        FROM users u
        JOIN branches b ON u.branch_id = b.id
        WHERE u.email = ?"
    ); 
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['branch_id'] = $user['branch_id'];
        $_SESSION['branch_name'] = $user['branch_name'];

        // check if password was reset
        if ($user['force_password_change']) {
        header("Location: change_password.php");
        exit;
    }

        // Redirect based on role
        switch ($user['role']) {
            case 'admin':
                header("Location: admin/admin_dashboard.php");
                break;
            case 'staff':
                header("Location: it_staff/it_staff_dashboard.php");
                break;
            default:
                header("Location: user/user_dashboard.php");
                break;
        }
        exit;
    } else {
        echo "Invalid login credentials.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>IT Support System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 py-10">
    <div class="max-w-md mx-auto bg-white p-6 shadow rounded">
        <h2 class="text-xl font-bold mb-4">LogIn</h2>
        <form method="POST" class="max-w-md mx-auto p-4 bg-white rounded shadow">
            <input type="email" name="email" placeholder="Email" required class="block w-full mb-2 border p-2" />
            <input type="password" name="password" placeholder="Password" required
                class="block w-full mb-2 border p-2" />
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Login</button>
        </form>