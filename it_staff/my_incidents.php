<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit;
}

$staff_id = $_SESSION['user_id'];
// Pagination settings
            $perPage = 5;
            $totalStmt = $pdo->prepare(
                "SELECT COUNT(*) FROM incidents WHERE assigned_to = ?" . 
                (isset($_GET['title']) && $_GET['title'] !== '' ? " AND title LIKE ?" : "")
            );
            if (isset($_GET['title']) && $_GET['title'] !== '') {
                $search = '%' . $_GET['title'] . '%';
                $totalStmt->execute([$staff_id, $search]);
            } else {
                $totalStmt->execute([$staff_id]);
            }
            $totalIncidents = (int)$totalStmt->fetchColumn();
            $totalPages = max(1, ceil($totalIncidents / $perPage));
            $page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $perPage;

            // Fetch paginated incidents
            if (isset($_GET['title']) && $_GET['title'] !== '') {
                $stmt = $pdo->prepare(
                    "SELECT i.*, u.name AS submitted_by_name, c.name AS name 
                     FROM incidents i 
                     LEFT JOIN users u ON i.submitted_by = u.id
                     LEFT JOIN kb_categories c ON i.category_id = c.id 
                     WHERE i.title LIKE ? AND i.assigned_to = ?
                     ORDER BY i.created_at DESC
                     LIMIT ? OFFSET ?"
                );
                $stmt->bindValue(1, '%' . $_GET['title'] . '%', PDO::PARAM_STR);
                $stmt->bindValue(2, $staff_id, PDO::PARAM_INT);
                $stmt->bindValue(3, $perPage, PDO::PARAM_INT);
                $stmt->bindValue(4, $offset, PDO::PARAM_INT);
                $stmt->execute();
                $incidents = $stmt->fetchAll();
            } else {
                $stmt = $pdo->prepare(
                    "SELECT i.*, u.name AS submitted_by_name, c.name AS name 
                     FROM incidents i 
                     LEFT JOIN users u ON i.submitted_by = u.id
                     LEFT JOIN kb_categories c ON i.category_id = c.id 
                     WHERE i.assigned_to = ?
                     ORDER BY i.created_at DESC
                     LIMIT ? OFFSET ?"
                );
                $stmt->bindValue(1, $staff_id, PDO::PARAM_INT);
                $stmt->bindValue(2, $perPage, PDO::PARAM_INT);
                $stmt->bindValue(3, $offset, PDO::PARAM_INT);
                $stmt->execute();
                $incidents = $stmt->fetchAll();
            }


// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['incident_id'], $_POST['status'])) {
    $incident_id = (int)$_POST['incident_id'];
    $status = $_POST['status'];
    $saved_amount = $_POST['saved_amount'] ?? '';

    if (!in_array($status, ['pending', 'fixed', 'not fixed'])) {
        $_SESSION['error'] = "Invalid status.";
    } else {

        // track update status
        $updated = false;

        // fetch the current status of the incident
        $stmt = $pdo->prepare("SELECT status FROM incidents WHERE id = ?");
        $stmt->execute([$incident_id]);
        $incid = $stmt->fetchColumn();

        // set status of incident
        if ($status === 'fixed') {
            // when fixed
            $update = $pdo->prepare("UPDATE incidents SET status = ?, fixed_date = NOW(), saved_amount = ? WHERE id = ?");
            if($update->execute([$status, $saved_amount, $incident_id])){
                $saved_amount = true;
                $updated = true;
            }
        } else {
            // when not fixed
            $update = $pdo->prepare("UPDATE incidents SET status = ? WHERE id = ?");
            if($update->execute([$status, $incident_id])) {
                $saved_amount = true;
                $updated = true;
            }
            // If previously fixed, reset fixed_date to null
            if ($incid === 'fixed') {
                $resetFixedDate = $pdo->prepare("UPDATE incidents SET fixed_date = NULL, saved_amount = NULL WHERE id = ?");
                if($resetFixedDate->execute([$incident_id])) {
                $saved_amount = true;
                $updated = true;
            }
            }
        }


        // After updating the incident status:
        if ($updated == true) {

            // Fetch the user who created the incident
            $stmtUser = $pdo->prepare("SELECT submitted_by FROM incidents WHERE id = ?");
            $stmtUser->execute([$incident_id]);
            $incidentUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if ($incidentUser) {
                $userId = $incidentUser['submitted_by'];
                $message = "Your incident (ID: $incident_id) has been marked as $status.";

                // Insert into notifications
                $stmtNotif = $pdo->prepare("INSERT INTO notifications (user_id, message, related_incident_id, is_seen, created_at) VALUES (?, ?, ?, 0, NOW())");
                $stmtNotif->execute([$userId, $message, $incident_id]);
            }
        }

        $log = $pdo->prepare("INSERT INTO incident_logs (incident_id, action, user_id, created_at) VALUES (?, ?, ?, NOW())");
        $log->execute([$incident_id, "Status changed to $status", $staff_id]);

        $_SESSION['success'] = "Incident status updated.";
    }

    header("Location: my_incidents.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Assigned Incidents</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

<!-- header and sidebar -->
<?php include '../includes/sidebar.php'; ?>
<?php include '../header.php'; ?>

<div class="max-w-7xl ms-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 pt-20 fade-in tech-border glow mt-8">
    <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">My Assigned Incidents</h2>
    <p class="text-center text-cyan-500 mb-6 font-mono">Manage and update your assigned incidents</p>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div id="success-message" class="mb-4 text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
            <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
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
    <?php if (isset($_SESSION['error'])): ?>
        <div id="error-message" class="mb-4 text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
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

    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <form method="get" class="flex items-center gap-2 w-full md:w-auto">
            <input type="text" name="title" placeholder="Search by Incident Title" value="<?= isset($_GET['title']) ? htmlspecialchars($_GET['title']) : '' ?>"
            class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono w-full md:w-64" />
            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest">
            Search
            </button>
        </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        $search = isset($_GET['id']) ? '%' . htmlspecialchars($_GET['id']) . '%' : '';
        $stmt = $pdo->prepare(
            "SELECT i.*, u.name AS submitted_by_name, c.name AS name 
               FROM incidents i 
               LEFT JOIN users u ON i.submitted_by = u.id
               LEFT JOIN kb_categories c ON i.category_id = c.id 
               WHERE i.id LIKE ? AND i.assigned_to = ?
               ORDER BY i.created_at DESC");
        $stmt->execute([$search, $staff_id]);
        $incidents = $stmt->fetchAll();
    }
    ?>

    <div class="overflow-x-auto rounded-xl shadow-inner">
        <?php if (count($incidents) === 0): ?>
            <p class="text-center text-cyan-600 font-mono py-8">No incidents assigned to you currently.</p>
        <?php else: ?>
            <div class="w-full overflow-x-auto">
                <table class="min-w-[900px] w-full border border-cyan-100 bg-white bg-opacity-90 font-mono text-cyan-900">
                    <thead>
                        <tr class="bg-cyan-50 text-cyan-700 text-left">
                            <th class="p-3 font-bold whitespace-nowrap">Title</th>
                            <th class="p-3 font-bold whitespace-nowrap">Description</th>
                            <th class="p-3 font-bold whitespace-nowrap">Submitted By</th>
                            <th class="p-3 font-bold whitespace-nowrap">Category</th>
                            <th class="p-3 font-bold whitespace-nowrap">Priority</th>
                            <th class="p-3 font-bold whitespace-nowrap">Status</th>
                            <th class="p-3 font-bold whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($incidents as $incident): ?>
                            <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition cursor-pointer"
                                onclick="window.location.href='incident_view.php?id=<?= $incident['id'] ?>'">
                                <td class="p-3 max-w-xs truncate" title="<?= htmlspecialchars($incident['title']) ?>">
                                    <?= htmlspecialchars($incident['title']) ?>
                                </td>
                                <td class="p-3 max-w-xs truncate" title="<?= htmlspecialchars($incident['description']) ?>">
                                    <?= htmlspecialchars($incident['description']) ?>
                                </td>
                                <td class="p-3 whitespace-nowrap"><?= htmlspecialchars($incident['submitted_by_name']) ?></td>
                                <td class="p-3 whitespace-nowrap"
                                    onclick="event.stopPropagation();">
                                    <!-- category -->
                                    <form method="POST" action="change_category.php" class="flex flex-col md:flex-row items-center gap-1">
                                        <input type="hidden" name="incident_id" value="<?= $incident['id'] ?>">
                                        <select name="category_id" class="border p-2 text-sm rounded-lg font-mono bg-cyan-50 border-cyan-200 focus:ring-2 focus:ring-cyan-300 transition"
                                            onchange="this.form.submit()">
                                            <?php
                                            // Fetch all categories
                                            $catStmt = $pdo->query("SELECT id, name FROM kb_categories ORDER BY name ASC");
                                            $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($categories as $cat):
                                            ?>
                                                <option value="<?= $cat['id'] ?>" <?= $incident['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </td>
                                <td class="p-3 whitespace-nowrap"><?= htmlspecialchars($incident['priority']) ?></td>
                                <td class="p-3 capitalize whitespace-nowrap">
                                    <?php 
                                // UI green for fixed, red for pending and dull gray for rejected.
                                if ($incident['status'] === 'fixed') {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-green-100 text-green-700 font-semibold">Fixed</span>';
                                } elseif ($incident['status'] === 'pending') {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-red-100 text-red-700 font-semibold animate-pulse">Pending</span>';
                                } elseif ($incident['status'] === 'not fixed') {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-orange-500 text-white font-semibold">Unfixed</span>';
                                } elseif ($incident['status'] === 'rejected') {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-gray-200 text-gray-500 font-semibold">Rejected</span>';
                                } elseif ($incident['status'] === 'rejected') {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-gray-200 text-gray-500 font-semibold">Rejected</span>';
                                } else {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 font-semibold">' . htmlspecialchars($incident['status']) . '</span>';
                                } 
                                ?>
                                </td>
                                <td class="p-3 whitespace-nowrap"
                                    onclick="event.stopPropagation();">
                                    <!-- status -->
                                    <form method="POST" class="flex flex-col md:flex-row items-center gap-2">
                                        <input type="hidden" name="incident_id" value="<?= $incident['id'] ?>">
                                        <select name="status" class="border p-2 text-sm rounded-lg font-mono bg-cyan-50 border-cyan-200 focus:ring-2 focus:ring-cyan-300 transition">
                                            <option value="pending" <?= strtolower($incident['status']) === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="fixed" <?= strtolower($incident['status']) === 'fixed' ? 'selected' : '' ?>>Fixed</option>
                                            <option value="not fixed" <?= strtolower($incident['status']) === 'not fixed' ? 'selected' : '' ?>>Not Fixed</option>
                                        </select>
                                        <input type="number" name="saved_amount" step="0.01" min="0" class="border p-2 rounded-lg font-mono w-32 bg-cyan-50 border-cyan-200 focus:ring-2 focus:ring-cyan-300 transition" placeholder="Estimated cost">
                                        <button type="submit" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-4 py-2 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
                                            Update
                                        </button>
                                        <a href="kb_list.php" class="bg-green-500 hover:bg-green-600 text-white font-bold rounded-lg shadow px-4 py-2 font-mono transition">Solution</a>
                                        <?php
                                            // Fetch image path for this incident
                                            $imgStmt = $pdo->prepare("SELECT `filepath` FROM files WHERE incident_id = ? LIMIT 1");
                                            $imgStmt->execute([$incident['id']]);
                                            $imgPath = $imgStmt->fetchColumn();
                                            if ($imgPath):
                                        ?>
                                            <a href="<?= htmlspecialchars($imgPath) ?>" target="_blank" class="bg-cyan-700 hover:bg-cyan-800 text-white font-bold rounded-lg shadow px-4 py-2 font-mono transition">Image</a>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php
            // Pagination controls
            if ($totalPages > 1): ?>
                <div class="flex justify-center mt-6 mb-2">
                    <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
                        <?php if ($page > 1): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>"
                               class="px-3 py-2 border border-cyan-200 bg-cyan-50 text-cyan-700 hover:bg-cyan-100 rounded-l-md font-mono">Prev</a>
                        <?php else: ?>
                            <span class="px-3 py-2 border border-cyan-200 bg-gray-100 text-gray-400 rounded-l-md font-mono cursor-not-allowed">Prev</span>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                               class="px-3 py-2 border-t border-b border-cyan-200 <?= $i === $page ? 'bg-cyan-300 text-white font-bold' : 'bg-cyan-50 text-cyan-700 hover:bg-cyan-100' ?> font-mono"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>"
                               class="px-3 py-2 border border-cyan-200 bg-cyan-50 text-cyan-700 hover:bg-cyan-100 rounded-r-md font-mono">Next</a>
                        <?php else: ?>
                            <span class="px-3 py-2 border border-cyan-200 bg-gray-100 text-gray-400 rounded-r-md font-mono cursor-not-allowed">Next</span>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
