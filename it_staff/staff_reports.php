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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
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
            <!-- Export to PDF Button -->
    <button id="export-pdf-btn"
        class="px-4 py-2 bg-gradient-to-r from-purple-500 via-blue-400 to-teal-300 hover:from-teal-400 hover:to-purple-500 text-white font-bold rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest">
        PDF Report
    </button>
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
                    <?php foreach (['pending','assigned','not fixed','fixed','fixed_confirmed','rejected'] as $status): ?>
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
            <?php
            // Show table rows only if any filter is set (not just non-empty)
            $filterSet = isset($_GET['from_date']) || isset($_GET['to_date']) || isset($_GET['status']) || isset($_GET['branch_id']) || isset($_GET['category_id']);
            if ($filterSet):
                foreach ($incidents as $incident): 
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
                    <td class="p-3"><?= $incident['created_at'] ?? '-' ?></td>
                    <td class="p-3"><?= $incident['fixed_date'] ?? '-' ?></td>
                    <td class="p-3"><?= $incident['remark'] ?? '-' ?></td>
                    <td class="p-3"><?= round(diffMinutes($incident['created_at'], $incident['fixed_date']), 2) ?> mins</td>
                    <td class="p-3"><?= round(diffMinutes($incident['created_at'], $incident['assigned_date']), 2) ?> mins</td>
                    <td class="p-3"><?= round($assigned_to_fixed, 2) ?> mins</td>
                </tr>
            <?php
                endforeach;
            else:
            ?>
                <tr>
                    <td colspan="13" class="text-center p-6 text-cyan-400 font-mono">Please use the filter above to view incidents.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Summary -->
    <div class="mt-6 bg-cyan-50 p-4 rounded-xl shadow-inner text-sm font-mono text-cyan-900 flex flex-col md:flex-row gap-4 justify-between">
        <div><strong>Total Incidents:</strong> <?= count($incidents) ?></div>
        <div><strong>Total Saved Amount:</strong> Br <?= number_format($total_saved, 2) ?></div>
        <div><strong>Total Time Taken to Fix:</strong> <?= round($total_time_seconds / 3600, 2) ?> minutes</div>
    </div>

    
    <!-- PDF Export Script with Summary -->
    <script>
    document.getElementById('export-pdf-btn').addEventListener('click', function () {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'pt', 'a4');
        doc.setFont('courier', 'normal');
        doc.setFontSize(12);

        // Cover page content
        const employeeName = <?= json_encode($_SESSION['name'] ?? ''); ?>;
        const dateGenerated = new Date().toLocaleString();
        const fromDate = <?= json_encode($filters['from_date'] ?? ''); ?>;
        const toDate = <?= json_encode($filters['to_date'] ?? ''); ?>;
        const jobPosition = <?= json_encode($_SESSION['job_position'] ?? ''); ?>;

        // Modern cover design
        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();

        // Gradient-like header bar
        doc.setFillColor(8, 145, 178);
        doc.rect(0, 0, pageWidth, 80, 'F');

        // Accent circle
        doc.setFillColor(59, 130, 246);
        doc.circle(pageWidth - 100, 60, 40, 'F');

        // Add full-width image banner at the top
        const bannerBase64 = '../uploads/letterHeader.jpg';
        const bannerHeight = 100;

        doc.addImage(bannerBase64, 'PNG', 0, 0, pageWidth, bannerHeight);

        doc.setFontSize(32);
        doc.setFont('courier', 'bold');
        doc.setTextColor(8, 145, 178);
        doc.text("IT Staff Incident Report", pageWidth / 2, bannerHeight + 67, { align: "center" });

        // Card-like info box
        doc.setFont('courier', 'normal');
        doc.setFillColor(236, 254, 255);
        const cardY = bannerHeight + 80;
        doc.roundedRect(60, cardY, pageWidth - 120, 210, 18, 18, 'F');

        doc.setFontSize(15);
        doc.setTextColor(8, 145, 178);
        doc.text("Employee Name:", 100, cardY + 50);
        doc.setTextColor(30, 41, 59);
        doc.text(employeeName, 260, cardY + 50);

        // Job Position label and blank line
        doc.setTextColor(8, 145, 178);
        doc.text("Job Position:", 100, cardY + 80);
        doc.setDrawColor(30, 41, 59);
        doc.setLineWidth(0.7);
        // doc.line(250, cardY + 80, 500, cardY + 80); // blank line
        doc.setTextColor(30, 41, 59);
        doc.text(jobPosition, 260, cardY + 80);

        doc.setFontSize(15);
        doc.setTextColor(8, 145, 178);
        doc.text("Date of Report Generation:", 100, cardY + 120);
        doc.setTextColor(30, 41, 59);
        doc.text(dateGenerated, 350, cardY + 120);

        doc.setTextColor(8, 145, 178);
        doc.text("Report Period:", 100, cardY + 160);
        doc.setTextColor(30, 41, 59);
        doc.text(`${fromDate || '-'} to ${toDate || '-'}`, 230, cardY + 160);

        // Footer for signatures (modern, spaced)
        doc.setDrawColor(8, 145, 178);
        doc.setLineWidth(1);
        doc.line(100, pageHeight - 110, 350, pageHeight - 110);
        doc.line(pageWidth - 350, pageHeight - 110, pageWidth - 100, pageHeight - 110);

        doc.setFontSize(13);
        doc.setFont('courier', 'bold');
        doc.setTextColor(8, 145, 178);
        doc.text("Employee Signature", 100, pageHeight - 95);
        doc.text("Manager Signature (Mikiyas Wendimu)", pageWidth - 350, pageHeight - 95);

        // Add a new page for the table and summary
        doc.addPage();
        doc.setFont('courier', 'normal');

        // Table headers
        const headers = [[
            "ID", "Title", "Status", "Branch", "Submitted By", "Category",
            "Saved Amount", "Created", "Fixed Date", "Remark",
            "Created - Fixed", "Created - Assigned", "Assigned - Fixed"
        ]];

        // Table body
        const rows = [];
        document.querySelectorAll('table tbody tr').forEach(tr => {
            const row = [];
            tr.querySelectorAll('td').forEach(td => {
                row.push(td.innerText.trim());
            });
            rows.push(row);
        });

        doc.setFontSize(12);
        doc.text("IT Staff Incident Report", 40, 40);
        doc.autoTable({
            head: headers,
            body: rows,
            startY: 60,
            styles: { font: "courier", fontSize: 9 },
            headStyles: { fillColor: [8, 145, 178] }
        });

        // Add summary below the table
        // Get summary values from the DOM
        var summaryDiv = document.querySelector('.mt-6.bg-cyan-50.flex');
        if (!summaryDiv) summaryDiv = document.querySelector('.mt-6.bg-cyan-50');
        var summaryItems = summaryDiv ? summaryDiv.querySelectorAll('div') : [];
        var totalIncidents = summaryItems.length > 0 ? summaryItems[0].innerText : '';
        var totalSaved = summaryItems.length > 1 ? summaryItems[1].innerText : '';
        var totalTime = summaryItems.length > 2 ? summaryItems[2].innerText : '';

        let summaryY = doc.lastAutoTable.finalY ? doc.lastAutoTable.finalY + 30 : 120;
        doc.setFontSize(13);
        doc.setTextColor(8, 145, 178);
        doc.text("Summary", 40, summaryY);

        doc.setFontSize(11);
        doc.setTextColor(30, 41, 59);
        doc.text(totalIncidents, 60, summaryY + 25);
        doc.text(totalSaved, 60, summaryY + 45);
        doc.text(totalTime, 60, summaryY + 65);

        doc.save('staff_incident_report.pdf');
    });
    </script>
</div>
</body>
</html>
