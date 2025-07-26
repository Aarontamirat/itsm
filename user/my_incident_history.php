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
$stmt = $pdo->prepare(
    "SELECT 
        i.*,
        c.name
    FROM 
        incidents i 
    LEFT JOIN 
        kb_categories c ON i.category_id = c.id
    WHERE 
        submitted_by = ? 
    ORDER BY 
        created_at 
    DESC LIMIT 
        ?, ?"
    );

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

<body class="bg-gray-100">

<!-- header and sidebar -->
      <?php include '../includes/sidebar.php'; ?>
  <div class="flex-1 ml-20">
    <?php include '../header.php'; ?>

    <div class="max-w-7xl ms-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 fade-in tech-border glow mt-8">
        <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Your Incident History</h2>
        <p class="text-center text-cyan-500 mb-6 font-mono">View and search your submitted IT support incidents</p>

        <?php
            // Filter by incident ID if provided in GET
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $incident_id = (int)$_GET['id'];
                $stmt = $pdo->prepare(
                    "SELECT i.*, c.name
                     FROM incidents i
                     LEFT JOIN kb_categories c ON i.category_id = c.id
                     WHERE i.id = :id AND i.submitted_by = :user_id"
                );
                $stmt->execute(['id' => $incident_id, 'user_id' => $user_id]);
                $incident = $stmt->fetch();
                if ($incident) {
                    echo '<div class="mb-8 p-6 bg-cyan-50 border border-cyan-200 rounded-lg shadow font-mono">';
                    echo '<h3 class="text-xl font-bold text-cyan-700 mb-2">Incident Details</h3>';
                    echo '<p><span class="font-semibold">Title:</span> ' . htmlspecialchars($incident['title']) . '</p>';
                    echo '<p><span class="font-semibold">Description:</span> ' . htmlspecialchars($incident['description']) . '</p>';
                    echo '<p><span class="font-semibold">Category:</span> ' . htmlspecialchars($incident['name']) . '</p>';
                    echo '<p><span class="font-semibold">Priority:</span> ' . htmlspecialchars($incident['priority']) . '</p>';
                    // Display status with color coding
                    $status_class = match ($incident['status']) {
                        'fixed' => 'text-green-600',
                        'fixed_confirmed' => 'text-green-600',
                        'pending' => 'text-red-600',
                        'not fixed' => 'text-orange-600',
                        'assigned' => 'text-yellow-600',
                        'rejected' => 'text-gray-600',
                        default => 'text-red-600',
                    };
                    echo '<p><span class="font-semibold">Status:</span> <span class="' . $status_class . '">' . htmlspecialchars($incident['status']) . '</span></p>';
                    echo '<p><span class="font-semibold">Rejection Reason:</span> ' . htmlspecialchars($incident['rejection_reason']) . '</p>';
                    echo '<p><span class="font-semibold">Rejected At:</span> ' . htmlspecialchars($incident['rejected_at']) . '</p>';
                    echo '<p><span class="font-semibold">Created:</span> ' . htmlspecialchars($incident['created_at']) . '</p>';
                    echo '</div>';
                } else {
                    echo '<div class="mb-8 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 font-mono">Incident not found.</div>';
                }
            }
        ?>

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

        <!-- Incidents Filters -->
        <form method="GET" class="mb-8 flex flex-col md:flex-row items-center gap-4 justify-center">
            
            <!-- title and description search -->
            <input type="text" name="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" placeholder="Search your incidents..." class="p-3 border border-cyan-200 rounded-lg bg-cyan-50 text-cyan-900 font-mono focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 w-full md:w-96" />
            
            <!-- status filter -->
            <select name="status" class="px-3 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 font-mono w-full md:w-36">
            <option value="">All Status</option>
            <?php
            $statuses = ['pending', 'fixed', 'not fixed', 'support', 'assigned', 'rejected', 'fixed_confirmed'];
            foreach ($statuses as $statusOpt):
            ?>
                <option value="<?= $statusOpt ?>" <?= (isset($_GET['status']) && $_GET['status'] === $statusOpt) ? 'selected' : '' ?>>
                <?= ucfirst(str_replace('_', ' ', $statusOpt)) ?>
                </option>
            <?php endforeach; ?>
            </select>

            <!-- date filter -->
            <label class="font-mono text-cyan-700">
            From:
            <input type="date" name="from_date" value="<?= isset($_GET['from_date']) ? htmlspecialchars($_GET['from_date']) : '' ?>" class="p-2 border border-cyan-200 rounded-lg bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200" />
            </label>
            <label class="font-mono text-cyan-700">
            To:
            <input type="date" name="to_date" value="<?= isset($_GET['to_date']) ? htmlspecialchars($_GET['to_date']) : '' ?>" class="p-2 border border-cyan-200 rounded-lg bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200" />
            </label>
            
            <!-- filter -->
            <button type="submit" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
            Filter
            </button>
        </form>

        <?php
        // // Date filter logic
        $from_date = isset($_GET['from_date']) && $_GET['from_date'] !== '' ? $_GET['from_date'] : null;
        $to_date = isset($_GET['to_date']) && $_GET['to_date'] !== '' ? $_GET['to_date'] : null;

        // Search logic
        $search = isset($_GET['search']) && $_GET['search'] !== '' ? '%' . $_GET['search'] . '%' : null;

        // Status logic
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';

        $where = ["i.submitted_by = :user_id"];
        $params = ['user_id' => $user_id];

        if ($search) {
            $where[] = "(i.title LIKE :search OR i.description LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }
        if ($status) {
            $where[] = "i.status = :status";
            $params['status'] = $status;
        }
        if ($from_date) {
            $where[] = "DATE(i.created_at) >= :from_date";
            $params['from_date'] = $from_date;
        }
        if ($to_date) {
            $where[] = "DATE(i.created_at) <= :to_date";
            $params['to_date'] = $to_date;
        }

        $where_sql = implode(' AND ', $where);

        // Count for pagination
        $countSql = "SELECT COUNT(*) FROM incidents i WHERE $where_sql";
        $countStmt = $pdo->prepare($countSql);
        foreach ($params as $key => $val) {
            $countStmt->bindValue(':' . $key, $val);
        }
        $countStmt->execute();
        $total_incidents = $countStmt->fetchColumn();
        $total_pages = ceil($total_incidents / $results_per_page);

        // Fetch incidents
        $sql = "SELECT i.*, c.name
            FROM incidents i
            LEFT JOIN kb_categories c ON i.category_id = c.id
            WHERE $where_sql
            ORDER BY i.created_at DESC
            LIMIT :start_from, :results_per_page";
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':start_from', $start_from, PDO::PARAM_INT);
        $stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
        $stmt->execute();
        $incidents = $stmt->fetchAll();
        ?>

        <?php
        // pagination logic
        if ($page < 1 || $page > $total_pages) {
            $page = 1; // Reset to first page if out of bounds
        }
        $start_from = ($page - 1) * $results_per_page;
        if ($start_from < 0) {
            $start_from = 0; // Ensure start_from is not negative
        }
        if ($total_incidents === 0) {
            $incidents = []; // No incidents found
        }
        // Display the incidents
        if ($total_incidents > 0 && empty($incidents)) {
            $_SESSION['error'] = 'No incidents found for the given filters.';
            header('Location: user/my_incident_history.php');
            exit;
        }

        ?>

        <div class="overflow-x-auto rounded-xl shadow-inner">
    <?php if (empty($incidents)): ?>
        <p class="text-center text-cyan-400 font-mono py-8">You have no incidents yet.</p>
    <?php else: ?>
        <form id="maintenanceForm" method="post" action="generate_request_form.php" target="_blank">
            <table class="w-full border border-cyan-100 bg-white bg-opacity-90 font-mono text-cyan-900">
                <thead>
                    <tr class="bg-cyan-50 text-cyan-700 text-left">
                        <th class="p-3 font-bold"><input type="checkbox" onclick="toggleAll(this)"></th>
                        <th class="p-3 font-bold">#</th>
                        <th class="p-3 font-bold">Title</th>
                        <th class="p-3 font-bold">Description</th>
                        <th class="p-3 font-bold">Category</th>
                        <th class="p-3 font-bold">Priority</th>
                        <th class="p-3 font-bold">Status</th>
                        <th class="p-3 font-bold">Created</th>
                        <th class="p-3 font-bold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incidents as $index => $incident): ?>
                        <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition">
                            <td class="p-3">
                                <input type="checkbox" name="selected[]" value="<?= htmlspecialchars(json_encode($incident)) ?>">
                            </td>
                            <td class="p-3"><?= $start_from + $index + 1 ?></td>
                            <td class="p-3"> <?= htmlspecialchars($incident['title']) ?> </td>
                            <td class="p-3"> <?= htmlspecialchars($incident['description']) ?> </td>
                            <td class="p-3"> <?= htmlspecialchars($incident['name']) ?> </td>
                            <td class="p-3"> <?= htmlspecialchars($incident['priority']) ?> </td>
                            <td class="p-3">
                                <?php
                                $status_class = match ($incident['status']) {
                                    'fixed', 'fixed_confirmed' => 'bg-green-400',
                                    'pending' => 'bg-red-400 animate-pulse',
                                    'not fixed' => 'bg-orange-400',
                                    'assigned' => 'bg-yellow-400',
                                    'rejected' => 'bg-gray-400',
                                    default => 'bg-red-400 animate-pulse',
                                };
                                ?>
                                <span class="<?= $status_class ?> rounded-lg px-1 text-white">
                                    <?= htmlspecialchars($incident['status']) ?>
                                </span>
                            </td>
                            <td class="p-3"> <?= htmlspecialchars($incident['created_at']) ?> </td>
                            <td class="p-3" onclick="event.stopPropagation();">
                                <?php if ($incident['status'] === 'fixed'): ?>
                                    <div class="notifList space-x-2 flex">
                                        <button 
                                            type="button"
                                            data-id="<?= $incident['id'] ?>" 
                                            class="confirm-btn bg-gradient-to-r from-cyan-800 via-cyan-700 to-green-600 hover:from-green-700 hover:to-cyan-600 text-white font-bold rounded-lg shadow-lg px-2 py-1 transform hover:scale-105 transition duration-300 font-mono"
                                        >
                                            Confirm
                                        </button>
                                        <button 
                                            type="button"
                                            data-id="<?= $incident['id'] ?>" 
                                            class="reopen-btn bg-gradient-to-r from-yellow-700 to-red-800 hover:from-red-700 hover:to-yellow-800 text-white font-bold rounded-lg shadow-lg px-2 py-1 transform hover:scale-105 transition duration-300 font-mono"
                                        >
                                            Reopen
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <span class="text-sm text-gray-400 italic">No actions</span>
                                <?php endif; ?>
                            </td>

                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>

            <div class="mt-4 text-right">
                <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded shadow">
                    Generate Maintenance Request Form
                </button>
            </div>
        </form>

        <script>
            function toggleAll(source) {
                checkboxes = document.querySelectorAll('input[name="selected[]"]');
                for (let i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = source.checked;
                }
            }
        </script>
    <?php endif; ?>
</div>


        <!-- Pagination -->
        <div class="mt-8">
            <nav class="flex justify-center">
                <ul class="flex space-x-2 font-mono">
                    <?php
                    $queryString = '';
                    // if ($search) {
                    //     $queryString = '&search=' . urlencode($_GET['search']);
                    // }
                    if ($status) {
                        $queryString = '&status=' . urlencode($_GET['status']);
                    }
                    if ($from_date) {
                        $queryString = '&from_date=' . urlencode($_GET['from_date']);
                    }
                    if ($to_date) {
                        $queryString = '&to_date=' . urlencode($_GET['to_date']);
                    }
                    for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li>
                            <a href="?page=<?= $i ?><?= $queryString ?>"
                                class="px-4 py-2 <?= $i == $page ? 'bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 text-white font-bold' : 'bg-cyan-50 text-cyan-700' ?> rounded-lg shadow transition">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>

    <!-- confirm and reopen logic -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.notifList').forEach(function(container) {
            container.addEventListener('click', function (e) {
                if (e.target.classList.contains('confirm-btn')) {
                    e.preventDefault();
                    const btn = e.target;
                    const id = btn.getAttribute('data-id');
                    fetch('../confirm_fixed.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ incident_id: id })
                    }).then(res => res.json()).then(resp => {
                        if (resp.success) {
                            btn.textContent = 'Confirmed';
                            btn.disabled = true;
                            btn.classList.add('opacity-60');
                            location.reload();
                        }
                    });
                }

                if (e.target.classList.contains('reopen-btn')) {
                    e.preventDefault();
                    const btn = e.target;
                    const id = btn.getAttribute('data-id');
                    fetch('../reopen_incident.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ incident_id: id })
                    }).then(res => res.json()).then(resp => {
                        if (resp.success) {
                            btn.textContent = 'Reopened';
                            btn.disabled = true;
                            btn.classList.add('opacity-60');
                            location.reload();
                        } else {
                            btn.textContent = 'Error';
                        }
                    });
                }
            });
        });
    });
</script>


</body>

</html>