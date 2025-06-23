<?php
session_start();
require '../config/db.php';

// check authorization
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'You are not authorized to access this page!';
    header("Location: ../login.php");
    exit;
}

// Fetch filter data
$branches = $pdo->query("SELECT id, name FROM branches")->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT id, name FROM kb_categories")->fetchAll(PDO::FETCH_ASSOC);
$staffs = $pdo->query("SELECT id, name FROM users WHERE role = 'staff'")->fetchAll(PDO::FETCH_ASSOC);
$users = $pdo->query("SELECT id, name FROM users WHERE role = 'user'")->fetchAll(PDO::FETCH_ASSOC);

// Handle filter values
$where = [];
$params = [];

if (!empty($_GET['branch'])) {
    $where[] = "i.branch_id = :branch";
    $params[':branch'] = $_GET['branch'];
}
if (!empty($_GET['status'])) {
    $where[] = "i.status = :status";
    $params[':status'] = $_GET['status'];
}
if (!empty($_GET['category'])) {
    $where[] = "i.category_id = :category";
    $params[':category'] = $_GET['category'];
}
if (!empty($_GET['staff'])) {
    $where[] = "i.assigned_to = :staff";
    $params[':staff'] = $_GET['staff'];
}
if (!empty($_GET['submitted_by'])) {
    $where[] = "i.submitted_by = :submitted_by";
    $params[':submitted_by'] = $_GET['submitted_by'];
}
if (!empty($_GET['from'])) {
    $where[] = "DATE(i.created_at) >= :from";
    $params[':from'] = $_GET['from'];
}
if (!empty($_GET['to'])) {
    $where[] = "DATE(i.created_at) <= :to";
    $params[':to'] = $_GET['to'];
}


if(isset($_GET['branch'])) {
    $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : 'WHERE 1';

    // Pagination settings
$limit = 10; // records per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total incidents for pagination
$countQuery = "SELECT COUNT(*) FROM incidents i $whereSQL";
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($params);
$totalIncidents = $countStmt->fetchColumn();
$totalPages = ceil($totalIncidents / $limit);

// Ensure $limit and $offset are integers to prevent SQL injection
$limit = (int)$limit;
$offset = (int)$offset;

$query = 
"SELECT i.*, 
           b.name AS branch_name,
           u1.name AS submitter,
           u2.name AS assignee,
           c.name AS category_name
    FROM incidents i
    LEFT JOIN branches b ON i.branch_id = b.id
    LEFT JOIN users u1 ON i.submitted_by = u1.id
    LEFT JOIN users u2 ON i.assigned_to = u2.id
    LEFT JOIN kb_categories c ON i.category_id = c.id
    $whereSQL
    ORDER BY i.created_at DESC
    LIMIT $limit OFFSET $offset
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$incidents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total saved amount
$totalQuery = "SELECT SUM(saved_amount) AS total_saved FROM incidents i $whereSQL";
$totalStmt = $pdo->prepare($totalQuery);
$totalStmt->execute($params);
$totalSaved = $totalStmt->fetchColumn() ?? 0.00;

// Monthly breakdown of saved_amount
$monthQuery = "
  SELECT DATE_FORMAT(i.created_at, '%Y-%m') AS month, SUM(i.saved_amount) AS total
  FROM incidents i
  $whereSQL
  GROUP BY month
  ORDER BY month DESC
";
$monthStmt = $pdo->prepare($monthQuery);
$monthStmt->execute($params);
$monthlySavings = $monthStmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable"></script>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-200">
    
        <!-- header and sidebar -->
    <?php include '../includes/sidebar.php'; ?>
    <?php include '../header.php'; ?>

    

    <div class="flex justify-end">
        <div class="w-full max-w-7xl mx-4 md:mx-12 lg:mx-24 bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 fade-in tech-border glow mt-8">
            <h1 class="text-3xl font-bold mb-6 mt-4 text-center">📊 Reports</h1>

            <!-- Filters -->
            <form method="GET" class="grid grid-cols-1 md:grid-cols-8 gap-4 mb-6 bg-white p-6 rounded shadow">
                    <select name="branch" class="p-2 border rounded">
                    <option value="">All Branches</option>
                    <?php foreach ($branches as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= ($_GET['branch'] ?? '') == $b['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['name']) ?>
                            </option>
                    <?php endforeach; ?>
                    </select>
                    <select name="status" class="p-2 border rounded">
                    <option value="">All Statuses</option>
                    <?php foreach (['pending', 'assigned', 'not fixed', 'fixed', 'fixed_confirmed', 'rejected'] as $status): ?>
                            <option value="<?= $status ?>" <?= ($_GET['status'] ?? '') == $status ? 'selected' : '' ?>>
                            <?= ucfirst($status) ?>
                            </option>
                    <?php endforeach; ?>
                    </select>
                    <select name="category" class="p-2 border rounded">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($_GET['category'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                            </option>
                    <?php endforeach; ?>
                    </select>
                    <select name="staff" class="p-2 border rounded">
                    <option value="">All IT Staff</option>
                    <?php foreach ($staffs as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= ($_GET['staff'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['name']) ?>
                            </option>
                    <?php endforeach; ?>
                    </select>
                    <select name="submitted_by" class="p-2 border rounded">
                    <option value="">All Submitters</option>
                    <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= ($_GET['submitted_by'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['name']) ?>
                            </option>
                    <?php endforeach; ?>
                    </select>
                    <input type="date" name="from" class="p-2 border rounded" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
                    <input type="date" name="to" class="p-2 border rounded" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
                    <button type="submit" class="col-span-1 md:col-span-3 bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
            </form>

            <!-- Charts Section -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4 max-w-3xl mx-auto">
                    <!-- Monthly Savings Bar Chart -->
                    <div class="bg-white p-4 rounded shadow">
                            <h2 class="text-base font-semibold mb-2">💹 Monthly Saved Amount</h2>
                            <div class="w-full" style="max-width:320px; margin:auto;">
                                    <canvas id="monthlySavingsChart" height="100"></canvas>
                            </div>
                    </div>
                    <!-- Status Distribution Pie Chart -->
                    <div class="bg-white p-4 rounded shadow">
                            <h2 class="text-base font-semibold mb-2">🗂️ Incident Status Distribution</h2>
                            <div class="w-full" style="max-width:320px; margin:auto;">
                                    <canvas id="statusPieChart" height="100"></canvas>
                            </div>
                    </div>
            </div>
            <script>
                    // Monthly Savings Chart Data
                    const monthlyLabels = <?= json_encode(array_column($monthlySavings, 'month')) ?>;
                    const monthlyData = <?= json_encode(array_map(fn($e) => (float)$e['total'], $monthlySavings)) ?>;

                    // Status Distribution Data
                    <?php
                            $statusCounts = [];
                            $statusList = ['pending', 'assigned', 'not fixed', 'fixed', 'fixed_confirmed', 'rejected'];
                            foreach ($statusList as $status) {
                                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM incidents i $whereSQL AND i.status = :status");
                                    $stmt->execute(array_merge($params, [':status' => $status]));
                                    $statusCounts[$status] = (int)$stmt->fetchColumn();
                            }
                    ?>
                    const statusLabels = <?= json_encode(array_map('ucfirst', $statusList)) ?>;
                    const statusData = <?= json_encode(array_values($statusCounts)) ?>;

                    // Monthly Savings Bar Chart
                    new Chart(document.getElementById('monthlySavingsChart'), {
                            type: 'bar',
                            data: {
                                    labels: monthlyLabels,
                                    datasets: [{
                                            label: 'Saved Amount (Birr)',
                                            data: monthlyData,
                                            backgroundColor: 'rgba(37, 99, 235, 0.7)'
                                    }]
                            },
                            options: {
                                    responsive: true,
                                    plugins: {
                                            legend: { display: false }
                                    },
                                    scales: {
                                            y: { beginAtZero: true }
                                    }
                            }
                    });

                    // Status Distribution Pie Chart
                    new Chart(document.getElementById('statusPieChart'), {
                            type: 'pie',
                            data: {
                                    labels: statusLabels,
                                    datasets: [{
                                            data: statusData,
                                            backgroundColor: [
                                                    '#f59e42', // pending
                                                    '#3b82f6', // assigned
                                                    '#ef4444', // not fixed
                                                    '#6666ff', // fixed
                                                    '#1a770e', // fixed_confirmed
                                                    '#6b7280'  // rejected
                                            ]
                                    }]
                            },
                            options: {
                                    responsive: true,
                                    plugins: {
                                            legend: { position: 'bottom' }
                                    }
                            }
                    });
            </script>

            <!-- Table -->
            <div class="bg-white p-6 rounded shadow overflow-x-auto">
                    <table class="min-w-full text-sm" id="reportTable">
                    <thead>
                            <tr class="bg-gray-200 text-left">
                            <th class="p-2">ID</th>
                            <th class="p-2">Title</th>
                            <th class="p-2">Status</th>
                            <th class="p-2">Branch</th>
                            <th class="p-2">Assigned To</th>
                            <th class="p-2">Submitted By</th>
                            <th class="p-2">Category</th>
                            <th class="p-2">Saved Amount</th>
                            <th class="p-2">Created</th>
                            <th class="p-2">Fixed Date</th>
                            <th class="p-2">Remark</th>
                            <th class="p-2">Issued to Fixed</th>
                            <th class="p-2">Issued to Assigned</th>
                            <th class="p-2">Assignment to Fixed</th>
                            </tr>
                    </thead>
                    <tbody>
                            <?php if ($incidents): ?>
                            <?php foreach ($incidents as $row): ?>
                                    <tr class="border-t">
                                            <td class="p-2"><?= $row['id'] ?></td>
                                            <td class="p-2"><?= htmlspecialchars($row['title']) ?></td>
                                            <td class="p-2"><?= htmlspecialchars($row['status']) ?></td>
                                            <td class="p-2"><?= htmlspecialchars($row['branch_name']) ?></td>
                                            <td class="p-2"><?= htmlspecialchars($row['assignee'] ?? '-') ?></td>
                                            <td class="p-2"><?= htmlspecialchars($row['submitter'] ?? '-') ?></td>
                                            <td class="p-2"><?= htmlspecialchars($row['category_name'] ?? '-') ?></td>
                                            <td class="p-2"><?= $row['saved_amount'] ? number_format($row['saved_amount'], 2) . ' Birr' : '-' ?></td>
                                            <td class="p-2"><?= $row['created_at'] ?></td>
                                            <td class="p-2"><?= $row['fixed_date'] ?? '-' ?></td>
                                            <td class="p-2"><?= $row['remark'] ?? '-' ?></td>

                                            <!-- from created_at to fixed status time take -->
                                             <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php if (($row['status'] === 'fixed') || ($row['status'] === 'fixed_confirmed') && $row['created_at'] && $row['fixed_date']): ?>
                                                            <?php
                                                            $created = new DateTime($row['created_at']);
                                                            $fixed = new DateTime($row['fixed_date']);
                                                            $interval = $created->diff($fixed);
                                                            echo $interval->format('%d days, %h hrs, %i mins');
                                                            ?>
                                                    <?php else: ?>
                                                            -
                                                    <?php endif; ?>
                                            </td>

                                            <!-- from created_at to assigned status time taken -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if (($row['status'] === 'assigned' || $row['status'] === 'fixed' || $row['status'] === 'fixed_confirmed') && $row['created_at'] && $row['assigned_date']): ?>
                                                    <?php
                                                    $created = new DateTime($row['created_at']);
                                                    $assigned = new DateTime($row['assigned_date']);
                                                    $interval = $created->diff($assigned);
                                                    echo $interval->format('%d days, %h hrs, %i mins');
                                                    ?>
                                            <?php else: ?>
                                                    -
                                            <?php endif; ?>
                                            </td>
                                            
                                            <!-- from assigned to fixed status time taken -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if (($row['status'] === 'fixed') || ($row['status'] === 'fixed_confirmed') && $row['fixed_date'] && $row['assigned_date']): ?>
                                                    <?php
                                                    $assigned = new DateTime($row['assigned_date']);
                                                    $fixed = new DateTime($row['fixed_date']);
                                                    $interval = $assigned->diff($fixed);
                                                    echo $interval->format('%d days, %h hrs, %i mins');
                                                    ?>
                                            <?php else: ?>
                                                    -
                                            <?php endif; ?>
                                            </td>

                                    </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr><td colspan="10" class="text-center py-4 text-red-500">No matching records found.</td></tr>
                            <?php endif; ?>
                    </tbody>
                    </table>

                    <!-- Pagination -->
            <div class="mt-6 flex justify-center items-center space-x-2">
            <?php if ($page > 1): ?>
                    <a href="<?= htmlspecialchars(preg_replace('/(&|\?)page=\d+/', '', $_SERVER['REQUEST_URI'])) . (strpos($_SERVER['REQUEST_URI'], '?') ? '&' : '?') ?>page=<?= $page - 1 ?>" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?= htmlspecialchars(preg_replace('/(&|\?)page=\d+/', '', $_SERVER['REQUEST_URI'])) . (strpos($_SERVER['REQUEST_URI'], '?') ? '&' : '?') ?>page=<?= $i ?>"
                    class="px-3 py-1 <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-gray-200' ?> rounded hover:bg-blue-500 hover:text-white">
                    <?= $i ?>
                    </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                    <a href="<?= htmlspecialchars(preg_replace('/(&|\?)page=\d+/', '', $_SERVER['REQUEST_URI'])) . (strpos($_SERVER['REQUEST_URI'], '?') ? '&' : '?') ?>page=<?= $page + 1 ?>" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Next</a>
            <?php endif; ?>
            </div>

                    <!-- Export Buttons -->
                    <div class="flex gap-4 mt-6">
                    <button onclick="exportToCSV()" class="bg-green-600 text-white px-4 py-2 rounded">📤 Export CSV</button>
                    <button onclick="exportToPDF()" class="bg-red-600 text-white px-4 py-2 rounded">📄 Export PDF</button>
                    </div>
            </div>

                    <!-- Totals -->
                    <div class="mt-10 bg-white p-6 rounded shadow">
                            <h2 class="text-xl font-semibold mb-4">Summary</h2>
                            <p class="text-lg mb-2"><strong>Total Incidents:</strong> <?= $totalIncidents ?></p>
                            <p class="text-lg mb-2"><strong>Total Saved Amount:</strong> <?= number_format($totalSaved, 2) ?> Birr</p>

                            <?php
                            // Calculate total time taken to fix (assigned_date to fixed_date) for all fixed incidents in current filter
                            $timeQuery = "
                            SELECT assigned_date, fixed_date
                            FROM incidents i
                            $whereSQL
                            AND i.status = 'fixed_confirmed'
                            AND assigned_date IS NOT NULL
                            AND fixed_date IS NOT NULL
                            ";
                            $timeStmt = $pdo->prepare($timeQuery);
                            $timeStmt->execute($params);
                            $totalSeconds = 0;
                            $countFixed = 0;
                            while ($row = $timeStmt->fetch(PDO::FETCH_ASSOC)) {
                            $assigned = new DateTime($row['assigned_date']);
                            $fixed = new DateTime($row['fixed_date']);
                            $diff = $fixed->getTimestamp() - $assigned->getTimestamp();
                            if ($diff > 0) {
                                    $totalSeconds += $diff;
                                    $countFixed++;
                            }
                            }
                            // Format total time taken
                            $days = floor($totalSeconds / 86400);
                            $hours = floor(($totalSeconds % 86400) / 3600);
                            $minutes = floor(($totalSeconds % 3600) / 60);
                            ?>
                            <p class="text-lg mb-4">
                            <strong>Total Time Taken to Fix (Assigned to Fixed):</strong>
                            <?= $countFixed > 0 ? "{$days} days, {$hours} hrs, {$minutes} mins" : '-' ?>
                            </p>

                            <?php if (count($monthlySavings) > 0): ?>
                            <h3 class="font-medium text-md mb-2">Monthly Breakdown:</h3>
                            <ul class="list-disc ml-6">
                            <?php
                                    foreach ($monthlySavings as $entry):
                                            // Get number of incidents and total time taken to fix for this month
                                            $month = $entry['month'];
                                            $incidentCountStmt = $pdo->prepare("
                                                    SELECT COUNT(*) FROM incidents i
                                                    WHERE DATE_FORMAT(fixed_date, '%Y-%m') = :month AND status = 'fixed_confirmed'
                                                    " . ($where ? " AND " . implode(' AND ', array_map(function($w) {
                                                            // Remove status filter for this count, since we already filter by status above
                                                            return strpos($w, 'i.status') === false ? $w : '1=1';
                                                    }, $where)) : '')
                                            );
                                            $incidentCountParams = array_merge($params, [':month' => $month]);
                                            $incidentCountStmt->execute($incidentCountParams);
                                            $fixedCount = $incidentCountStmt->fetchColumn();

                                            // Total time taken to fix for this month
                                            $timeStmt = $pdo->prepare("
                                                    SELECT assigned_date, fixed_date FROM incidents i
                                                    WHERE DATE_FORMAT(fixed_date, '%Y-%m') = :month AND status = 'fixed_confirmed'
                                                    AND assigned_date IS NOT NULL AND fixed_date IS NOT NULL
                                                    " . ($where ? " AND " . implode(' AND ', array_map(function($w) {
                                                            return strpos($w, 'i.status') === false ? $w : '1=1';
                                                    }, $where)) : '')
                                            );
                                            $timeStmt->execute($incidentCountParams);
                                            $totalSeconds = 0;
                                            $count = 0;
                                            while ($row = $timeStmt->fetch(PDO::FETCH_ASSOC)) {
                                                    $assigned = new DateTime($row['assigned_date']);
                                                    $fixed = new DateTime($row['fixed_date']);
                                                    $diff = $fixed->getTimestamp() - $assigned->getTimestamp();
                                                    if ($diff > 0) {
                                                            $totalSeconds += $diff;
                                                            $count++;
                                                    }
                                            }
                                            $days = floor($totalSeconds / 86400);
                                            $hours = floor(($totalSeconds % 86400) / 3600);
                                            $minutes = floor(($totalSeconds % 3600) / 60);
                            ?>
                                    <li>
                                            <?= htmlspecialchars($entry['month']) ?>: 
                                            <strong><?= number_format($entry['total'], 2) ?> Birr</strong>
                                            <br>
                                            <strong>Incidents Fixed:</strong> <?= $fixedCount ?>
                                            <br>
                                            <strong>Total Time to Fix:</strong>
                                            <?= $count > 0 ? "{$days} days, {$hours} hrs, {$minutes} mins" : '-' ?>
                                    </li>
                            <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                    </div>
            </div>
    </div>
    
<script>
function exportToCSV() {
    const table = document.getElementById("reportTable");
    let csv = [];
    // Get headers
    const headers = [];
    table.querySelectorAll("thead th").forEach(th => {
        headers.push('"' + th.innerText.replace(/"/g, '""') + '"');
    });
    csv.push(headers.join(","));

    // Get rows
    table.querySelectorAll("tbody tr").forEach(tr => {
        const row = [];
        tr.querySelectorAll("td").forEach(td => {
            row.push('"' + td.innerText.replace(/"/g, '""') + '"');
        });
        if(row.length) csv.push(row.join(","));
    });

    // Download CSV
    const csvContent = csv.join("\r\n");
    const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "incident_report.csv";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'pt', 'a4');
    const table = document.getElementById("reportTable");

    // Title
    doc.setFontSize(18);
    doc.text("Incident Report", doc.internal.pageSize.getWidth() / 2, 40, { align: "center" });

    // Prepare columns and rows
    const headers = [];
    const data = [];
    const ths = table.querySelectorAll("thead th");
    ths.forEach(th => headers.push(th.innerText.trim()));

    const trs = table.querySelectorAll("tbody tr");
    trs.forEach(tr => {
        const row = [];
        tr.querySelectorAll("td").forEach(td => {
            row.push(td.innerText.trim());
        });
        if(row.length) data.push(row);
    });

    // AutoTable
    doc.autoTable({
        head: [headers],
        body: data,
        startY: 60,
        theme: 'grid',
        headStyles: {
            fillColor: [59, 130, 246],
            textColor: 255,
            fontStyle: 'bold',
            halign: 'center'
        },
        bodyStyles: {
            halign: 'center',
            valign: 'middle'
        },
        alternateRowStyles: {
            fillColor: [245, 245, 245]
        },
        styles: {
            fontSize: 9,
            cellPadding: 4,
            overflow: 'linebreak'
        },
        margin: { left: 20, right: 20 }
    });

    // Get summary and monthly breakdown from the DOM
    let summaryText = "";
    const summaryDiv = document.querySelector('.mt-10.bg-white.p-6.rounded.shadow');
    if (summaryDiv) {
        // Get all <p> and <li> inside summary
        const ps = summaryDiv.querySelectorAll('p');
        ps.forEach(p => {
            summaryText += p.innerText + "\n";
        });

        const monthlyHeader = summaryDiv.querySelector('h3');
        if (monthlyHeader) {
            summaryText += "\n" + monthlyHeader.innerText + "\n";
        }
        const lis = summaryDiv.querySelectorAll('ul li');
        lis.forEach(li => {
            summaryText += "- " + li.innerText + "\n";
        });
    }

    // Add summary below the table
    let finalY = doc.lastAutoTable ? doc.lastAutoTable.finalY + 30 : 100;
    doc.setFontSize(13);
    doc.text("Summary", 30, finalY);
    doc.setFontSize(10);
    doc.text(summaryText, 30, finalY + 20, { maxWidth: doc.internal.pageSize.getWidth() - 60 });

    // Footer
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(10);
        doc.text(`Page ${i} of ${pageCount}`, doc.internal.pageSize.getWidth() - 60, doc.internal.pageSize.getHeight() - 10);
    }

    doc.save('incident_report.pdf');
}
</script>
</body>
</html>
