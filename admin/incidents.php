<?php
session_start();
require '../config/db.php';

// Restrict to Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch branches for filter
$branches = $pdo->query("SELECT * FROM branches")->fetchAll();
$branchFilter = $_GET['branch_id'] ?? '';

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
if ($branchFilter) {
    $stmt = $pdo->prepare("SELECT 
      incidents.*,
      users.id AS user_id,
      users.name AS assigned_to,
      c.name AS name
    FROM 
      incidents
    LEFT JOIN 
      users ON incidents.assigned_to = users.id
    LEFT JOIN
      kb_categories c ON incidents.category_id = c.id
    WHERE 
      incidents.branch_id = ?
    ORDER BY created_at DESC LIMIT ?, ?");
    $stmt->bindValue(1, $branchFilter, PDO::PARAM_INT);
    $stmt->bindValue(2, $start_from, PDO::PARAM_INT);
    $stmt->bindValue(3, $results_per_page, PDO::PARAM_INT);
    $stmt->execute();
    $incidents = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT 
      i.*,
      u.id AS user_id,
      u.name AS assigned_to,
      c.name AS name
    FROM 
        incidents i
    LEFT JOIN 
        users u ON i.assigned_to = u.id
    LEFT JOIN
        kb_categories c ON i.category_id = c.id
    ORDER BY 
        created_at DESC LIMIT ?, ?");
    
$stmt->bindValue(1, $start_from, PDO::PARAM_INT);
$stmt->bindValue(2, $results_per_page, PDO::PARAM_INT);
$stmt->execute();
$incidents = $stmt->fetchAll();
}


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

<body class="bg-gray-100">

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

<!-- header and sidebar -->
      <?php include '../includes/sidebar.php'; ?>
    <?php include '../header.php'; ?>

    <div class="max-w-6xl ms-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 fade-in tech-border glow mt-8">
        <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Incident Management</h2>
        <p class="text-center text-cyan-500 mb-1 font-mono">Manage and track IT support incidents</p>

        <!-- Success/Error Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div id="success-message" class="mb-4 text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
                <?= htmlspecialchars($_GET['success']) ?>
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
            <?php elseif (isset($_GET['error'])): ?>
            <div id="error-message" class="mb-4 text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
                <?= htmlspecialchars($_GET['error']) ?>
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
            <div class="flex gap-2">
                <a href="export_csv.php" class="inline-block px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-mono font-semibold shadow transition">Export CSV</a>
                <a href="export_pdf.php" class="inline-block px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-mono font-semibold shadow transition">Export PDF</a>
            </div>

            <?php
            // Filter by incident ID if provided in GET
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $incidentId = (int)$_GET['id'];
                $stmt = $pdo->prepare(
                    "SELECT 
                        incidents.*,
                        c.name AS name
                    FROM 
                        incidents
                    LEFT JOIN
                        kb_categories c ON incidents.category_id = c.id
                    WHERE 
                        incidents.id = ?
                    ORDER BY 
                        incidents.created_at DESC"
                );
                $stmt->execute([$incidentId]);
                $incidents = $stmt->fetchAll();
                // Override pagination since only one result is shown
                $total_pages = 1;
                $page = 1;
                $start_from = 0;
            }
            ?>

            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="search" placeholder="Search incidents..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono" />
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest">
                    Search
                </button>
            </form>
            <form method="GET" class="flex items-center gap-2">
                <label for="branchFilter" class="font-mono text-cyan-700 font-semibold">Branch:</label>
                <select name="branch_id" id="branchFilter" class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono" onchange="this.form.submit()">
                    <option value="">All Branches</option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>" <?= ($branchFilter == $branch['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($branch['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <?php
        // Search logic (move above table to avoid duplicate queries)
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
            $search = '%' . htmlspecialchars($_GET['search']) . '%';
            $stmt = $pdo->prepare(
                "SELECT 
                    incidents.*,
                    users.id AS user_id,
                    users.name AS assigned_to,
                    c.name AS name
                FROM 
                    incidents
                LEFT JOIN 
                    users ON incidents.assigned_to = users.id
                LEFT JOIN
                    kb_categories c ON incidents.category_id = c.id
                WHERE 
                    incidents.title LIKE ? 
                ORDER BY 
                    incidents.created_at DESC"
            );
            $stmt->execute([$search]);
            $incidents = $stmt->fetchAll();
        }
        ?>

        <div class="overflow-x-auto rounded-xl shadow-inner">
            <table class="w-full border border-cyan-100 bg-white bg-opacity-90 font-mono text-cyan-900">
                <thead>
                    <tr class="bg-cyan-50 text-cyan-700 text-left">
                        <th class="p-3 font-bold">#</th>
                        <th class="p-3 font-bold">Title</th>
                        <th class="p-3 font-bold">Category</th>
                        <th class="p-3 font-bold">Priority</th>
                        <th class="p-3 font-bold">Status</th>
                        <th class="p-3 font-bold">Assigned To</th>
                        <th class="p-3 font-bold">Fixed Date</th>
                        <th class="p-3 font-bold">Rejected Reason</th>
                        <th class="p-3 font-bold">Remark</th>
                        <th class="p-3 font-bold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incidents as $index => $incident): ?>
                        <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition">
                            <td class="p-3"><?= $start_from + $index + 1 ?></td>
                            <td class="p-3"><?= htmlspecialchars($incident['title']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($incident['name']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($incident['priority']) ?></td>
                            <td class="p-3">
                                <?php 
                                // UI green for fixed, red for pending and dull gray for rejected.
                                if ($incident['status'] === 'fixed') {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-green-100 text-green-700 font-semibold">Fixed</span>';
                                } elseif ($incident['status'] === 'fixed_confirmed') {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-green-400 text-white font-semibold">Confirmed</span>';
                                } elseif ($incident['status'] === 'pending') {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-red-100 text-red-700 font-semibold animate-pulse">Pending</span>';
                                } elseif ($incident['status'] === 'not fixed') {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-orange-500 text-white font-semibold">Unfixed</span>';
                                } elseif ($incident['status'] === 'assigned') {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-semibold">assigned</span>';
                                } elseif ($incident['status'] === 'rejected') {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-gray-200 text-gray-500 font-semibold">Rejected</span>';
                                } else {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 font-semibold">' . htmlspecialchars($incident['status']) . '</span>';
                                } 
                                ?>
                            </td>
                            <td class="p-3"><?= htmlspecialchars($incident['assigned_to']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($incident['fixed_date']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($incident['rejection_reason']) ?? '' ?></td>
                            <td class="p-3"><?= htmlspecialchars($incident['remark']) ?></td>
                            <td class="p-3">
                                <div class="flex flex-col md:flex-row gap-2 md:items-center">
                                    <?php
                                        if (($incident['status'] !== 'fixed') && ($incident['status'] !== 'rejected')) {
                                            ?>
                                    <a href="<?= ($incident['assigned_to'] == '') || ($incident['assigned_to'] == null) ? 'assign_incidents.php?id=' . $incident['id'] : 'reassign_incidents.php?id='.$incident['id'] ?>" class="bg-green-400 hover:bg-green-500 text-white font-bold px-3 py-1 rounded-lg shadow transition w-full md:w-auto"> <?= ($incident['assigned_to'] == '') || ($incident['assigned_to'] == null) ? 'Assign' : 'Reassign' ?> </a>
                                    <?php
                                    } else{
                                        
                                    }
                                    ?>
                   
                                    <form action="update_incident_status.php" method="POST" class="inline-block">
                                        <input type="hidden" name="incident_id" value="<?= $incident['id'] ?>" />
                                        <?php
                                        if (($incident['status'] !== 'fixed') && ($incident['status'] !== 'rejected')) {
                                            echo '<div class="flex gap-2">
                                                <select name="status" class="px-2 py-1 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 font-mono">
                                                    <option value="pending" '.($incident["status"] === "pending" ? "selected" : "").'>Pending</option>
                                                    <option value="assigned" '.($incident["status"] === "assigned" ? "selected" : "").'>Assigned</option>
                                                </select>
                                                <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-white font-bold px-3 py-1 rounded-lg shadow transition">Update</button>
                                            </div>';
                                        }
                                        ?>
                                    </form>

                                    <!-- Reject Button (for Admin/Staff) -->
                                     <?php
                                     if (($incident['status'] === 'pending')) {
                                        echo '<button class="bg-red-600 text-white px-3 py-1 rounded" onclick="openRejectModal(' . $incident['id'] .')">Reject</button>';
                                     }
                                        ?>

                                        <!-- Reject Modal -->
                                        <div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
                                        <div class="bg-white p-6 rounded-lg w-full max-w-md">
                                            <h2 class="text-xl font-bold mb-4">Reject Incident</h2>
                                            <form id="rejectForm">
                                            <input type="hidden" name="incident_id" id="reject_incident_id">
                                            <textarea name="rejection_reason" required class="w-full p-2 border rounded" placeholder="Enter reason..."></textarea>
                                            <div class="mt-4 flex justify-end gap-2">
                                                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-400 rounded">Cancel</button>
                                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Reject</button>
                                            </div>
                                            </form>
                                        </div>
                                        </div>

                                    <a href="incident_history.php?id=<?= $incident['id'] ?>" class="bg-blue-400 hover:bg-blue-500 text-white font-bold px-3 py-1 rounded-lg shadow transition w-full md:w-auto text-center">History</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            <nav class="flex justify-center">
                <ul class="flex space-x-2 font-mono">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li>
                            <a href="?page=<?= $i ?><?= $branchFilter ? '&branch_id=' . $branchFilter : '' ?>"
                                class="px-4 py-2 <?= $i == $page ? 'bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 text-white font-bold' : 'bg-cyan-50 text-cyan-700' ?> rounded-lg shadow transition">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>

    <script>
  function openRejectModal(id) {
    document.getElementById('reject_incident_id').value = id;
    document.getElementById('rejectModal').classList.remove('hidden');
  }

  function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
  }

  document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('reject_incident.php', {
      method: 'POST',
      body: formData
    }).then(res => res.json())
      .then(data => {
        if (data.success) {
          alert('Incident rejected.');
          location.reload();
        } else {
          alert('Error rejecting: ' + data.message);
        }
      });
  });
</script>

</body>

</html>