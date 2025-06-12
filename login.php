<?php
session_start();
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $sessionExpired = isset($_POST['error']) ? $_POST['error'] : '';

    if ($sessionExpired) {
        $_SESSION['error'] = $sessionExpired;
        header("Location: login.php");
        exit;
    }

    // Validate email
    if (empty($email)) {
        $_SESSION['error'] = "Email is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
    }

    // Validate password
    if (empty($password)) {
        $_SESSION['error'] = "Password is required.";
    }
    if (strlen($password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters.";
    }

    if (!isset($_SESSION['error'])) {
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
            $_SESSION['last_activity'] = time();

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
            $_SESSION['error'] = "Invalid login credentials.";
            header("Location: login.php");
            exit;
        }
    } else {
        header("Location: login.php");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>IT Support System - Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    .float-anim {
        animation: float 3s ease-in-out infinite;
    }

    .fade-in {
        animation: fadeIn 1s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .glow {
        box-shadow: 0 0 16px 2px #67e8f9, 0 0 32px 4px #6ee7b7;
    }

    .tech-border {
        border-image: linear-gradient(90deg, #e0f2fe 0%, #67e8f9 50%, #6ee7b7 100%) 1;
        border-width: 2px;
        border-style: solid;
    }
    </style>
</head>

<body class="bg-white min-h-screen flex items-center justify-center">
    <div class="absolute top-6 left-1/2 -translate-x-1/2 text-center z-10">
        <span
            class="inline-block px-6 py-2 rounded-full bg-gradient-to-r from-cyan-50 via-cyan-100 to-green-100 text-cyan-700 font-bold text-lg shadow font-mono border border-cyan-100 tracking-widest">
            Lucy Insurance IT Support System
        </span>
    </div>
    <div class="relative w-full max-w-md mx-auto">
        <div class="absolute -top-16 left-1/2 -translate-x-1/2 float-anim">
            <svg width="90" height="90" viewBox="0 0 90 90" fill="none">
                <defs>
                    <radialGradient id="techGlow" cx="50%" cy="50%" r="50%">
                        <stop offset="0%" stop-color="#e0f2fe" />
                        <stop offset="70%" stop-color="#67e8f9" />
                        <stop offset="100%" stop-color="#6ee7b7" />
                    </radialGradient>
                </defs>
                <circle cx="45" cy="45" r="40" stroke="url(#techGlow)" stroke-width="5" fill="#fff" />
                <rect x="25" y="35" width="40" height="20" rx="6" fill="#e0f2fe" stroke="#67e8f9" stroke-width="2" />
                <rect x="35" y="40" width="20" height="10" rx="2" fill="#6ee7b7" opacity="0.7" />
                <circle cx="38" cy="45" r="2" fill="#67e8f9" />
                <circle cx="52" cy="45" r="2" fill="#67e8f9" />
                <rect x="42" y="48" width="6" height="2" rx="1" fill="#22d3ee" />
            </svg>
        </div>
        <div class="bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 pt-20 fade-in tech-border glow">
            <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Login</h2>
            <p class="text-center text-cyan-500 mb-1 font-mono">Sign in to your IT Support account</p>
            <p class="text-center text-green-500 mb-6 font-mono text-xs">Property of Lucy Insurance</p>
            <?php if (isset($_SESSION['error'])): ?>
            <div id="error-message"
                class="mb-4 text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2 text-centefont-mono font-semibold opacity-0 transition-opacity duration-500">
                <?php
                    echo htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']);
                    ?>
            </div>
            <script>
            // Fade in
            setTimeout(function() {
                var el = document.getElementById('error-message');
                if (el) el.style.opacity = '1';
            }, 10);
            // Fade out after 3 seconds
            setTimeout(function() {
                var el = document.getElementById('error-message');
                if (el) el.style.opacity = '0';
            }, 5010);
            </script>
            <?php endif; ?>
            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-cyan-700 font-semibold mb-1 font-mono" for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="you@email.com" required
                        class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono" />
                </div>
                <div>
                    <label class="block text-cyan-700 font-semibold mb-1 font-mono" for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Password" required
                        class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-green-200 focus:outline-none transition duration-200 font-mono" />
                </div>
                <button type="submit"
                    class="w-full py-2 px-4 bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest">
                    Login
                </button>
            </form>
        </div>
    </div>
</body>

</html>