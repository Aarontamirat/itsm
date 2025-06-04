<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    $_SESSION['error'] = "Access denied. Please log in.";
    header("Location: ../login.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $priority = $_POST['priority'];
    // $branch = $_POST['branch_name'];
    $user_id = $_SESSION['user_id'];
    $branch_id = $_SESSION['branch_id'];
    $category_id = $_POST['category_id'];

    if (empty($title)) $errors[] = 'Title is required.';
    if (empty($description)) $errors[] = 'Description is required.';
    if (!in_array($priority, ['Low', 'Medium', 'High'])) $errors[] = 'Invalid priority.';
    if (empty($branch_id)) $errors[] = 'Unknown branch, please contact your system.';
    if (empty($category_id)) $errors[] = 'Please select an incident category.';

    if (empty($errors)) {
        // Insert incident
        $stmt = $pdo->prepare("INSERT INTO incidents (title, description, category_id, priority, status, submitted_by, branch_id, created_at) VALUES (?, ?, ?, ?, 'Pending', ?, ?, NOW())");
        $stmt->execute([$title, $description, $category_id, $priority, $user_id, $branch_id]);
        $incident_id = $pdo->lastInsertId();

        // Handle file upload
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_name = basename($_FILES['file']['name']);
            $target_path = "../uploads/" . time() . "_" . $file_name;

            if (move_uploaded_file($file_tmp, $target_path)) {
                $stmt = $pdo->prepare("INSERT INTO files (incident_id, filepath, uploaded_at) VALUES (?, ?, NOW())");
                $stmt->execute([$incident_id, $target_path]);
            }
        }

        // Inside incident submission logic
        $admins = $pdo->query("SELECT id FROM users WHERE role = 'admin'")->fetchAll();
        foreach ($admins as $admin) {
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, related_incident_id) VALUES (?, ?, ?)");
            $stmt->execute([$admin['id'], "New incident reported", $incident_id]);
        }
        // Add to incident logs
        $log = $pdo->prepare("INSERT INTO incident_logs (incident_id, action, user_id, created_at) VALUES (?, ?, ?, NOW())");
        $log->execute([$incident_id, "Incident reported by User ID: $user_id", $user_id]);
        
        $message = "Incident submitted successfully!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Report Incident</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<!-- header and sidebar -->
      <?php include '../includes/sidebar.php'; ?>
  <div class="flex-1 ml-20">
    <?php include '../header.php'; ?>

    <div class="max-w-3xl mx-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 pt-16 fade-in tech-border glow mt-8">
        <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Report New Incident</h2>
        <p class="text-center text-cyan-500 mb-6 font-mono">Submit a new IT support incident</p>

        <?php if (!empty($errors)): ?>
            <div id="error-message" class="mb-4 text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
                <ul class="list-disc list-inside">
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
        <?php if (isset($message)): ?>
            <div id="success-message" class="mb-4 text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
                <?= $message ?>
            </div>
            <script>
                setTimeout(function() {
                    var el = document.getElementById('success-message');
                    if (el) el.style.opacity = '1';
                }, 10);
                setTimeout(function() {
                    var el = document.getElementById('success-message');
                    if (el) el.style.opacity = '0';
                }, 3010);
            </script>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6 mt-6 font-mono">
            <!-- incident title -->
            <div>
                <label class="block text-cyan-700 font-semibold mb-1">Title</label>
                <input type="text" name="title" class="w-full p-3 border border-cyan-200 rounded-lg bg-cyan-50 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition" required>
            </div>

            <!-- incident description -->
            <div>
                <label class="block text-cyan-700 font-semibold mb-1">Description</label>
                <textarea name="description" class="w-full p-3 border border-cyan-200 rounded-lg bg-cyan-50 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition" rows="4" required></textarea>
            </div>

            <!-- incident category -->
            <div>
                <label for="category" class="block text-cyan-700 font-semibold mb-1">Incident Category</label>
                <select name="category_id" id="category" class="block w-full mt-1 p-3 border border-cyan-200 rounded-lg bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition">
                    <option value="">-- Select Category --</option>
                    <?php
                    $stmt = $pdo->query("SELECT id, name FROM kb_categories ORDER BY name ASC");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <!-- incident priority -->
            <div>
                <label class="block text-cyan-700 font-semibold mb-1">Priority</label>
                <select name="priority" class="w-full p-3 border border-cyan-200 rounded-lg bg-cyan-50 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition" required>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>

            <!-- incident file upload -->
            <div>
                <label class="block text-cyan-700 font-semibold mb-1">Optional File Upload</label>
                <input type="file" name="file" class="w-full p-2 border border-cyan-200 rounded-lg bg-cyan-50 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition">
            </div>

            <!-- incident branch -->
            <div>
                <label class="block text-cyan-700 font-semibold mb-1">Branch</label>
                <input type="text" disabled name="branch" value="<?= htmlspecialchars($_SESSION['branch_name']) ?>" class="w-full p-3 border border-cyan-200 rounded-lg bg-cyan-50 text-cyan-900" readonly>
            </div>

            <!-- submit button -->
            <div class="flex flex-col md:flex-row gap-4 justify-center items-center mt-6">
                <button type="submit" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-8 py-3 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
                    Submit Incident
                </button>
                <a href="user_dashboard.php" class="text-cyan-600 hover:underline font-semibold font-mono">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>