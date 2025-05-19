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
    $user_id = $_SESSION['user_id'];

    if (empty($title)) $errors[] = 'Title is required.';
    if (empty($description)) $errors[] = 'Description is required.';
    if (!in_array($priority, ['Low', 'Medium', 'High'])) $errors[] = 'Invalid priority.';

    if (empty($errors)) {
        // Insert incident
        $stmt = $pdo->prepare("INSERT INTO incidents (title, description, priority, status, submitted_by, created_at) VALUES (?, ?, ?, 'Pending', ?, NOW())");
        $stmt->execute([$title, $description, $priority, $user_id]);
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
        $notificationStmt = $pdo->prepare("INSERT INTO notifications (message, user_id) VALUES (?, ?)");
        $notificationStmt->execute(["New incident submitted by user ID: $user_id", 1]);  // Admin's user_id = 1

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

<body class="bg-gray-100 p-6">
    <div class="max-w-lg mx-auto bg-white p-6 shadow rounded">
        <h2 class="text-xl font-bold mb-4">Report New Incident</h2>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 text-red-800 p-3 mb-4 rounded">
                <ul class="list-disc ml-5">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <!-- message -->
        <?php if (isset($message)): ?>
            <div class="mb-4 text-green-600"><?= $message ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block">Title</label>
                <input type="text" name="title" class="w-full p-2 border rounded" required>
            </div>

            <div>
                <label class="block">Description</label>
                <textarea name="description" class="w-full p-2 border rounded" rows="4" required></textarea>
            </div>

            <div>
                <label class="block">Priority</label>
                <select name="priority" class="w-full p-2 border rounded" required>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>

            <div>
                <label class="block">Optional File Upload</label>
                <input type="file" name="file" class="w-full">
            </div>

            <div>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Submit Incident</button>
                <a href="user_dashboard.php" class="ml-3 text-gray-600">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>