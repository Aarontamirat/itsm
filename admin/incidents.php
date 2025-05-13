<?php
session_start();
require '../config/db.php';

// Restrict to Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Define how many results per page
$results_per_page = 10;

// Find out the total number of incidents
$stmt = $pdo->query("SELECT COUNT(*) FROM incidents");
$total_incidents = $stmt->fetchColumn();

// Calculate total pages
$total_pages = ceil($total_incidents / $results_per_page);

// Get current page from URL, default to 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Fetch incidents for the current page
$stmt = $pdo->prepare("SELECT * FROM incidents ORDER BY created_at DESC LIMIT ?, ?");
$stmt->bindValue(1, $start_from, PDO::PARAM_INT);
$stmt->bindValue(2, $results_per_page, PDO::PARAM_INT);
$stmt->execute();
$incidents = $stmt->fetchAll();

// Fetch IT Staff for assignment
$staffStmt = $pdo->query("SELECT id, name FROM users WHERE role = 'staff'");
$staff = $staffStmt->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Incident Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">

    <!-- css -->
    <style>
@keyframes slide-in {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slide-in {
  animation: slide-in 0.3s ease-out;
}
</style>

    <div class="max-w-5xl mx-auto bg-white p-6 shadow rounded">

        <!-- exports -->
        <a href="export_csv.php" class="bg-blue-600 text-white px-4 py-2 rounded">Export CSV</a>
        <a href="export_pdf.php" class="bg-red-600 text-white px-4 py-2 rounded">Export PDF</a>

        <!-- search form -->
        <form method="GET" class="mb-4">
            <input type="text" name="search" placeholder="Search incidents..." class="p-2 border rounded" />
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded">Search</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
            // Handle search
            $search = isset($_GET['search']) ? '%' . htmlspecialchars($_GET['search']) . '%' : '';
            $stmt = $pdo->prepare("SELECT * FROM incidents WHERE title LIKE ? ORDER BY created_at DESC");
            $stmt->execute([$search]);
            $incidents = $stmt->fetchAll();
        }
        ?>

        <!-- table -->
        <h2 class="text-2xl font-bold mb-4">Incident Management</h2>

                    <!-- form submission message -->
                             <?php if (isset($_GET['success'])): ?>
                            <div id="successMsg" class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4 animate-slide-in">
                                <?= htmlspecialchars($_GET['success']) ?>
                            </div>
                            <script>
                                // Auto-hide after 3 seconds
                                setTimeout(() => {
                                document.getElementById('successMsg').style.display = 'none';
                                }, 3000);
                            </script>
                            <?php endif; ?>


        <table class="w-full border mt-4">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-2">#</th>
                    <th class="p-2">Title</th>
                    <th class="p-2">Priority</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Assigned To</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($incidents as $index => $incident): ?>
                <tr class="border-t">
                    <td class="p-2"><?= $index + 1 ?></td>
                    <td class="p-2"><?= htmlspecialchars($incident['title']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($incident['priority']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($incident['status']) ?></td>
                    <td class="p-2"><?= $incident['assigned_to'] ? 'Assigned' : 'Not Assigned' ?></td>
                    <td class="p-2">
                        <form action="assign_incidents.php" method="POST" class="inline-block">
                            <input type="hidden" name="incident_id" value="<?= $incident['id'] ?>" />
                            <select name="assigned_to" class="p-2 border rounded">
                                <option name="staff_id" value="">Assign Staff</option>
                                <?php foreach ($staff as $member): ?>
                                <option value="<?= $member['id'] ?>"><?= $member['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded"><?= $incident['assigned_to'] == '0' ? 'Assign' : 'Reassign' ?></button>
                        </form>
                        <form action="update_incident_status.php" method="POST" class="inline-block ml-2">
                            <input type="hidden" name="incident_id" value="<?= $incident['id'] ?>" />
                            <select name="status" class="p-2 border rounded">
                                <option value="pending" <?= $incident['status'] === 'pending' ? 'selected' : '' ?>>
                                    Pending</option>
                                <option value="assigned"
                                    <?= $incident['status'] === 'assigned' ? 'selected' : '' ?>>Assigned</option>
                                <option value="fixed" <?= $incident['status'] === 'fixed' ? 'selected' : '' ?>>
                                    Fixed</option>
                                <option value="rejected" <?= $incident['status'] === 'rejected' ? 'selected' : '' ?>>
                                    Rejected</option>
                            </select>
                            <button type="submit" class="bg-yellow-600 text-white px-3 py-1 rounded">Update
                                Status</button>
                        </form>
                        <a href="incident_history.php?id=<?= $incident['id'] ?>" 
   class="text-blue-600 hover:underline text-sm">
  View History
</a>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-4">
            <nav class="flex justify-center">
                <ul class="flex space-x-2">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li>
                        <a href="?page=<?= $i ?>"
                            class="px-4 py-2 <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-gray-200' ?> rounded">
                            <?= $i ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</body>

</html>