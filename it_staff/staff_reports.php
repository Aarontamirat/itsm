<?php
session_start();
require_once '../config/db.php';

// Ensure only IT Staff can access
if ($_SESSION['role'] !== 'staff') {
    header('Location: ../unauthorized.php');
    exit;
}

$staff_id = $_SESSION['user_id'];

// Filter inputs
$filters = [
    'from_date' => $_GET['from_date'] ?? null,
    'to_date' => $_GET['to_date'] ?? null,
    'status' => $_GET['status'] ?? '',
    'branch_id' => $_GET['branch_id'] ?? '',
    'category_id' => $_GET['category_id'] ?? '',
];

$query = "SELECT i.*, 
                u.name AS submitted_by_name, 
                b.name AS branch_name, 
                c.name AS category_name 
         FROM incidents i
         LEFT JOIN users u ON i.submitted_by = u.id
         LEFT JOIN branches b ON i.branch_id = b.id
         LEFT JOIN kb_categories c ON i.category_id = c.id
         WHERE i.assigned_to = :staff_id";

// Append filters
$params = ['staff_id' => $staff_id];

if ($filters['from_date']) {
    $query .= " AND DATE(i.created_at) >= :from_date";
    $params['from_date'] = $filters['from_date'];
}
if ($filters['to_date']) {
    $query .= " AND DATE(i.created_at) <= :to_date";
    $params['to_date'] = $filters['to_date'];
}
if ($filters['status']) {
    $query .= " AND i.status = :status";
    $params['status'] = $filters['status'];
}
if ($filters['branch_id']) {
    $query .= " AND i.branch_id = :branch_id";
    $params['branch_id'] = $filters['branch_id'];
}
if ($filters['category_id']) {
    $query .= " AND i.category_id = :category_id";
    $params['category_id'] = $filters['category_id'];
}

$query .= " ORDER BY i.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$incidents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Summaries
$total_saved = 0;
$total_time_seconds = 0;

function diffMinutes($start, $end) {
  if (!$start || !$end) return null;
  $diff = strtotime($end) - strtotime($start);
  return $diff > 0 ? $diff / 60 : 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Staff Reports</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<!-- header and sidebar -->
<?php include '../includes/sidebar.php'; ?>
<?php include '../header.php'; ?>

<div class="max-w-7xl ms-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 fade-in tech-border glow mt-8">
    <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">IT Staff Incident Report</h2>
    <p class="text-center text-cyan-500 mb-1 font-mono">Incidents assigned to you</p>

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

    <!-- Filters & Export -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-8">
        <div class="flex gap-2 justify-center md:justify-start">
            <a href="export_staff_report_csv.php"
               class="inline-block px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-mono font-semibold shadow transition text-center">
                Export CSV
            </a>
            <a href="export_staff_report_pdf.php"
               class="inline-block px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-mono font-semibold shadow transition text-center">
                Export PDF
            </a>
        </div>
        <form method="get" class="w-full flex flex-col lg:flex-row flex-wrap items-stretch lg:items-center gap-2 lg:gap-4">
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-1 w-full lg:w-auto">
                <label for="from_date" class="font-mono text-cyan-700 font-semibold lg:mr-2">From:</label>
                <input type="date" name="from_date" id="from_date" value="<?= $filters['from_date'] ?>"
                       class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono w-full lg:w-auto">
            </div>
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-1 w-full lg:w-auto">
                <label for="to_date" class="font-mono text-cyan-700 font-semibold lg:mr-2">To:</label>
                <input type="date" name="to_date" id="to_date" value="<?= $filters['to_date'] ?>"
                       class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono w-full lg:w-auto">
            </div>
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-1 w-full lg:w-auto">
                <label for="status" class="font-mono text-cyan-700 font-semibold lg:mr-2">Status:</label>
                <select name="status" id="status"
                        class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono w-full lg:w-auto">
                    <option value="">All Status</option>
                    <?php foreach (['pending','assigned','not fixed','fixed','rejected'] as $status): ?>
                        <option value="<?= $status ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-1 w-full lg:w-auto">
                <label for="branch_id" class="font-mono text-cyan-700 font-semibold lg:mr-2">Branch:</label>
                <select name="branch_id" id="branch_id"
                        class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono w-full lg:w-auto">
                    <option value="">All Branches</option>
                    <?php
                    $branches = $pdo->query("SELECT id, name FROM branches")->fetchAll();
                    foreach ($branches as $b):
                    ?>
                        <option value="<?= $b['id'] ?>" <?= $filters['branch_id'] == $b['id'] ? 'selected' : '' ?>><?= $b['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-1 w-full lg:w-auto">
                <label for="category_id" class="font-mono text-cyan-700 font-semibold lg:mr-2">Category:</label>
                <select name="category_id" id="category_id"
                        class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono w-full lg:w-auto">
                    <option value="">All Categories</option>
                    <?php
                    $categories = $pdo->query("SELECT id, name AS category_name FROM kb_categories")->fetchAll();
                    foreach ($categories as $c):
                    ?>
                        <option value="<?= $c['id'] ?>" <?= $filters['category_id'] == $c['id'] ? 'selected' : '' ?>><?= $c['category_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-center w-full lg:w-auto">
                <button type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest w-full lg:w-auto">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto rounded-xl shadow-inner">
        <table class="w-full border border-cyan-100 bg-white bg-opacity-90 font-mono text-cyan-900">
            <thead>
                <tr class="bg-cyan-50 text-cyan-700 text-left">
                    <th class="p-3 font-bold">ID</th>
                    <th class="p-3 font-bold">Title</th>
                    <th class="p-3 font-bold">Status</th>
                    <th class="p-3 font-bold">Branch</th>
                    <th class="p-3 font-bold">Submitted By</th>
                    <th class="p-3 font-bold">Category</th>
                    <th class="p-3 font-bold">Saved Amount</th>
                    <th class="p-3 font-bold">Created</th>
                    <th class="p-3 font-bold">Fixed Date</th>
                    <th class="p-3 font-bold">Remark</th>
                    <th class="p-3 font-bold">Created → Fixed</th>
                    <th class="p-3 font-bold">Created → Assigned</th>
                    <th class="p-3 font-bold">Assigned → Fixed</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($incidents as $incident): 
                $total_saved += $incident['saved_amount'] ?? 0;
                $assigned_to_fixed = diffMinutes($incident['assigned_date'], $incident['fixed_date']);
                $total_time_seconds += ($assigned_to_fixed ?? 0) * 3600;
            ?>
                <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition">
                    <td class="p-3"><?= $incident['id'] ?></td>
                    <td class="p-3"><?= htmlspecialchars($incident['title']) ?></td>
                    <td class="p-3 capitalize">
                        <span class="<?= $incident['status'] === 'fixed' ? 'bg-green-400 text-white px-1 rounded-lg' : ($incident['status'] === 'not fixed' ? 'bg-red-400 text-white px-1 rounded-lg' : 'bg-cyan-300 text-cyan-900 px-1 rounded-lg') ?>">
                            <?= ucfirst($incident['status']) ?>
                        </span>
                    </td>
                    <td class="p-3"><?= $incident['branch_name'] ?></td>
                    <td class="p-3"><?= $incident['submitted_by_name'] ?></td>
                    <td class="p-3"><?= $incident['category_name'] ?></td>
                    <td class="p-3">Br <?= number_format($incident['saved_amount'], 2) ?></td>
                    <td class="p-3"><?= $incident['created_at'] ?></td>
                    <td class="p-3"><?= $incident['fixed_date'] ?></td>
                    <td class="p-3"><?= $incident['status'] === 'not fixed' ? 'Unresolved' : '' ?></td>
                    <td class="p-3"><?= round(diffMinutes($incident['created_at'], $incident['fixed_date']), 2) ?> mins</td>
                    <td class="p-3"><?= round(diffMinutes($incident['created_at'], $incident['assigned_date']), 2) ?> mins</td>
                    <td class="p-3"><?= round($assigned_to_fixed, 2) ?> mins</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Summary -->
    <div class="mt-6 bg-cyan-50 p-4 rounded-xl shadow-inner text-sm font-mono text-cyan-900 flex flex-col md:flex-row gap-4 justify-between">
        <div><strong>Total Incidents:</strong> <?= count($incidents) ?></div>
        <div><strong>Total Saved Amount:</strong> Br <?= number_format($total_saved, 2) ?></div>
        <div><strong>Total Time Taken to Fix:</strong> <?= round($total_time_seconds / 3600, 2) ?> minutes</div>
    </div>
</div>
</body>
</html>
