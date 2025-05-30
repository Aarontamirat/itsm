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

    <div class="max-w-lg mx-auto bg-white mt-6 p-6 shadow rounded">
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

        <!-- incident title -->
            <div>
                <label class="block">Title</label>
                <input type="text" name="title" class="w-full p-2 border rounded" required>
            </div>

            <!-- incident desciption -->
            <div>
                <label class="block">Description</label>
                <textarea name="description" class="w-full p-2 border rounded" rows="4" required></textarea>
            </div>

            <!-- incident category -->
             <div>
                <label for="category" class="block">Incident Category</label>
                <select name="category_id" id="category" class="block w-full mt-1 p-2 border border-gray-300 rounded-md">
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
                <label class="block">Priority</label>
                <select name="priority" class="w-full p-2 border rounded" required>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>

            <!-- incident file upload -->
            <div>
                <label class="block">Optional File Upload</label>
                <input type="file" name="file" class="w-full">
            </div>

            <!-- incident branch -->
            <div>
                <label class="block">Branch</label>
                <input type="text" disabled name="branch" value="<?= htmlspecialchars($_SESSION['branch_name']) ?>" class="w-full p-2 border rounded" readonly>
            </div>

            <!-- submit button -->
            <div>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Submit Incident</button>
                <a href="user_dashboard.php" class="ml-3 text-gray-600">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>