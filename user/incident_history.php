<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Define how many results per page
$results_per_page = 10;

// Find out the total number of incidents for the user
$stmt = $pdo->prepare("SELECT COUNT(*) FROM incidents WHERE submitted_by = ?");
$stmt->execute([$user_id]);
$total_incidents = $stmt->fetchColumn();

// Calculate total pages
$total_pages = ceil($total_incidents / $results_per_page);

// Get current page from URL, default to 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Fetch incidents for the current page
$stmt = $pdo->prepare("SELECT * FROM incidents WHERE submitted_by = ? ORDER BY created_at DESC LIMIT ?, ?");
$stmt->bindValue(1, $user_id, PDO::PARAM_INT);
$stmt->bindValue(2, $start_from, PDO::PARAM_INT);
$stmt->bindValue(3, $results_per_page, PDO::PARAM_INT);
$stmt->execute();
$incidents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Incident History</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 shadow rounded">
        <h2 class="text-2xl font-bold mb-4">Your Incident History</h2>
        <!-- search for history -->
        <form method="GET" class="mb-4">
            <input type="text" name="search" placeholder="Search your incidents..." class="p-2 border rounded" />
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded">Search</button>
        </form>

        <?php
        $search = isset($_GET['search']) ? '%' . htmlspecialchars($_GET['search']) . '%' : '';
        $stmt = $pdo->prepare("SELECT * FROM incidents WHERE submitted_by = ? AND title LIKE ? ORDER BY created_at DESC");
        $stmt->execute([$user_id, $search]);
        $incidents = $stmt->fetchAll();
        ?>

        <!-- table -->
        <?php if (empty($incidents)): ?>
            <p>You have no incidents yet.</p>
        <?php else: ?>
            <table class="w-full border mt-4">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="p-2">#</th>
                        <th class="p-2">Title</th>
                        <th class="p-2">Priority</th>
                        <th class="p-2">Status</th>
                        <th class="p-2">Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incidents as $index => $incident): ?>
                        <tr class="border-t">
                            <td class="p-2"><?= $index + 1 ?></td>
                            <td class="p-2"><?= htmlspecialchars($incident['title']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($incident['priority']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($incident['status']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($incident['created_at']) ?></td>
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
        <?php endif; ?>
    </div>
</body>

</html>