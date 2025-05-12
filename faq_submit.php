<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit;
}

$incident_id = isset($_GET['incident']) ? (int)$_GET['incident'] : null;
$staff_id = $_SESSION['user_id'];

// Handle FAQ submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $solution = trim($_POST['solution']);
    $linked_incident = $_POST['linked_incident'] ? (int)$_POST['linked_incident'] : null;

    if ($title && $category && $solution) {
        $stmt = $pdo->prepare("INSERT INTO faqs (title, category, solution, created_by, linked_incident, created_at) 
                               VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$title, $category, $solution, $staff_id, $linked_incident]);

        $_SESSION['success'] = "FAQ added successfully.";
        header("Location: my_incidents.php");
        exit;
    } else {
        $_SESSION['error'] = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit FAQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-xl mx-auto bg-white p-6 shadow rounded">
        <h2 class="text-xl font-bold mb-4">Submit Solution to FAQ</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 text-red-800 p-3 mb-4 rounded">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="linked_incident" value="<?= $incident_id ?>">

            <div>
                <label class="block text-sm font-medium">FAQ Title</label>
                <input type="text" name="title" class="w-full border p-2 rounded" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Category</label>
                <input type="text" name="category" class="w-full border p-2 rounded" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Solution</label>
                <textarea name="solution" rows="6" class="w-full border p-2 rounded" required></textarea>
            </div>

            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save to FAQ</button>
                <a href="my_incidents.php" class="ml-4 text-gray-600 underline">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
