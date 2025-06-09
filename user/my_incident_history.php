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
                        'pending' => 'text-red-600',
                        'not fixed' => 'text-orange-600',
                        'assigned' => 'text-yellow-600',
                        'rejected' => 'text-gray-600',
                        default => 'text-red-600',
                    };
                    echo '<p><span class="font-semibold">Status:</span> <span class="' . $status_class . '">' . htmlspecialchars($incident['status']) . '</span></p>';
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

        <!-- Date Filter -->
        <form method="GET" class="mb-8 flex flex-col md:flex-row items-center gap-4 justify-center">
            <label class="font-mono text-cyan-700">
            From:
            <input type="date" name="from_date" value="<?= isset($_GET['from_date']) ? htmlspecialchars($_GET['from_date']) : '' ?>" class="p-2 border border-cyan-200 rounded-lg bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200" />
            </label>
            <label class="font-mono text-cyan-700">
            To:
            <input type="date" name="to_date" value="<?= isset($_GET['to_date']) ? htmlspecialchars($_GET['to_date']) : '' ?>" class="p-2 border border-cyan-200 rounded-lg bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200" />
            </label>
            <button type="submit" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
            Filter
            </button>
        </form>

        <?php
        // Date filter logic
        $from_date = isset($_GET['from_date']) && $_GET['from_date'] !== '' ? $_GET['from_date'] : null;
        $to_date = isset($_GET['to_date']) && $_GET['to_date'] !== '' ? $_GET['to_date'] : null;

        // Search logic
        $search = isset($_GET['search']) && $_GET['search'] !== '' ? '%' . $_GET['search'] . '%' : null;

        $where = ["i.submitted_by = :user_id"];
        $params = ['user_id' => $user_id];

        if ($search) {
            $where[] = "(i.title LIKE :search OR i.description LIKE :search)";
            $params['search'] = $search;
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

        <!-- Search -->
        <form method="GET" class="mb-8 flex flex-col md:flex-row items-center gap-4 justify-center">
            <input type="text" name="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" placeholder="Search your incidents..." class="p-3 border border-cyan-200 rounded-lg bg-cyan-50 text-cyan-900 font-mono focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 w-full md:w-96" />
            <button type="submit" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
                Search
            </button>
        </form>

        <?php
        // Search logic
        $search = isset($_GET['search']) && $_GET['search'] !== '' ? '%' . $_GET['search'] . '%' : null;
        if ($search) {
            $stmt = $pdo->prepare(
                "SELECT i.*, c.name
                FROM incidents i
                LEFT JOIN kb_categories c ON i.category_id = c.id
                WHERE i.submitted_by = ? AND (i.title LIKE ? OR i.description LIKE ?)
                ORDER BY i.created_at DESC
                LIMIT ?, ?"
            );
            $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
            $stmt->bindValue(2, $search, PDO::PARAM_STR);
            $stmt->bindValue(3, $search, PDO::PARAM_STR);
            $stmt->bindValue(4, $start_from, PDO::PARAM_INT);
            $stmt->bindValue(5, $results_per_page, PDO::PARAM_INT);
            $stmt->execute();
            $incidents = $stmt->fetchAll();

            // For pagination with search
            $countStmt = $pdo->prepare(
                "SELECT COUNT(*) FROM incidents WHERE submitted_by = ? AND (title LIKE ? OR description LIKE ?)"
            );
            $countStmt->execute([$user_id, $search, $search]);
            $total_incidents = $countStmt->fetchColumn();
            $total_pages = ceil($total_incidents / $results_per_page);
        }
        ?>

        <div class="overflow-x-auto rounded-xl shadow-inner">
            <?php if (empty($incidents)): ?>
                <p class="text-center text-cyan-400 font-mono py-8">You have no incidents yet.</p>
            <?php else: ?>
                <table class="w-full border border-cyan-100 bg-white bg-opacity-90 font-mono text-cyan-900">
                    <thead>
                        <tr class="bg-cyan-50 text-cyan-700 text-left">
                            <th class="p-3 font-bold">#</th>
                            <th class="p-3 font-bold">Title</th>
                            <th class="p-3 font-bold">Description</th>
                            <th class="p-3 font-bold">Category</th>
                            <th class="p-3 font-bold">Priority</th>
                            <th class="p-3 font-bold">Status</th>
                            <th class="p-3 font-bold">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($incidents as $index => $incident): ?>
                            <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition">
                                <td class="p-3"><?= $start_from + $index + 1 ?></td>
                                <td class="p-3"><?= htmlspecialchars($incident['title']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($incident['description']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($incident['name']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($incident['priority']) ?></td>
                                <td class="p-3">
                                    <?php
                                    $status_class = match ($incident['status']) {
                                        'fixed' => 'bg-green-400 rounded-lg px-1 text-white',
                                        'pending' => 'bg-red-400 rounded-lg px-1 text-white animate-pulse',
                                        'not fixed' => 'bg-orange-400 rounded-lg px-1 text-white',
                                        'assigned' => 'bg-yellow-400 rounded-lg px-1 text-white',
                                        'rejected' => 'bg-gray-400 rounded-lg px-1 text-white',
                                        default => 'bg-red-400 rounded-lg px-1 text-white animate-pulse',
                                    };
                                    ?>
                                    <span class="<?= $status_class ?>"><?= htmlspecialchars($incident['status']) ?></span>
                                </td>
                                <td class="p-3"><?= htmlspecialchars($incident['created_at']) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            <nav class="flex justify-center">
                <ul class="flex space-x-2 font-mono">
                    <?php
                    $queryString = '';
                    if ($search) {
                        $queryString = '&search=' . urlencode($_GET['search']);
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
</body>

</html>