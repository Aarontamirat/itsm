<?php
require '../config/db.php'; session_start();
if($_SESSION['role']!=='admin') { 
  header("Location: ../login.php");
  exit;
 }

// filters
$from = $_GET['from'] ?? ''; $to = $_GET['to'] ?? ''; $status = $_GET['status'] ?? ''; $staff = $_GET['staff'] ?? '';
$where = []; $params = [];
if($from){ $where[]="DATE(p.created_at)>=:from"; $params[':from']=$from;}
if($to){ $where[]="DATE(p.created_at)<=:to"; $params[':to']=$to;}
if($status){ $where[]="p.status=:status"; $params[':status']=$status;}
if($staff){ $where[]="p.assigned_to=:staff"; $params[':staff']=$staff;}
$whereSQL = $where ? 'WHERE '.implode(' AND ',$where) : '';

// data
$stmt = $pdo->prepare("
  SELECT p.*, u.name AS staff_name
  FROM projects p
  LEFT JOIN users u ON p.assigned_to=u.id
  $whereSQL
  ORDER BY p.created_at DESC
");
$stmt->execute($params);
$projects = $stmt->fetchAll();

// count completed and uncompleted projects
$completedProjectsCount = 0;
$uncompletedProjectsCount = 0;
foreach ($projects as $p) {
    if ($p['status'] === 'fixed' || $p['status'] === 'confirmed fixed') {
        $completedProjectsCount++;
    } else {
        $uncompletedProjectsCount++;
    }
}
// calculate total time spent on projects by deducting completed
$totalTimeSpent = 0;
foreach ($projects as $p) {
    if ($p['status'] === 'fixed' || $p['status'] === 'confirmed fixed') {
        // Assuming assigned_date and completion_date is stored in timestamp format
        $assignedDate = new DateTime($p['assigned_at']);
        $completionDate = new DateTime($p['completion_date']);
        $interval = $completionDate->diff($assignedDate);
        $totalTimeSpent += $interval->h + ($interval->days * 24); // Convert days to hours
    }
}

// summary
$total = count($projects);
$byStatus = []; $byStaff = []; $uncompletedProjectsByWeek = [];
foreach($projects as $p){
  $byStatus[$p['status']] = isset($byStatus[$p['status']]) ? $byStatus[$p['status']] + 1 : 1;
  $staffKey = $p['staff_name'] ?: 'Unassigned';
  $byStaff[$staffKey] = isset($byStaff[$staffKey]) ? $byStaff[$staffKey] + 1 : 1;
foreach ($projects as $p) {
    if ($p['status'] != 'fixed' && $p['status'] != 'confirmed fixed') {
        // Calculate weeks since project creation
        $createdDate = new DateTime($p['created_at']);
        $now = new DateTime();
        $interval = $now->diff($createdDate);
        $weeksPassed = floor($interval->days / 7);
        
        // Populate the uncompletedProjectsByWeek array
        // Use project title as key and weeks passed as value
        $uncompletedProjectsByWeek[$p['title']] = $weeksPassed;
    }
}

}

$staffList = $pdo->query("SELECT id,name FROM users WHERE role='staff'")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Project Reports</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
</head>
<body class="bg-gray-100">

<?php include '../includes/sidebar.php'; ?>
<?php include '../header.php'; ?>

<div class="max-w-7xl ms-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 fade-in tech-border glow mt-8">
  <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">📊 Project Reports</h2>
  <p class="text-center text-cyan-500 mb-6 font-mono">Overview and analytics of all IT projects</p>

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

  <form class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8 font-mono" method="get">
    <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
      <div class="flex flex-col md:flex-row items-center gap-2">
        <label for="from" class="text-cyan-700 font-semibold">From:</label>
        <input type="date" id="from" name="from" value="<?=$from?>" class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono">
      </div>
      <div class="flex flex-col md:flex-row items-center gap-2">
        <label for="to" class="text-cyan-700 font-semibold">To:</label>
        <input type="date" id="to" name="to" value="<?=$to?>" class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono">
      </div>
      <div class="flex flex-col md:flex-row items-center gap-2">
        <label for="status" class="text-cyan-700 font-semibold">Status:</label>
        <select name="status" id="status" class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono">
          <option value="">All Status</option>
          <?php foreach(['pending','assigned','in progress','needs redo','fixed','confirmed fixed'] as $s): ?>
            <option <?= $status===$s? 'selected':'' ?> value="<?=$s?>"><?=ucfirst($s)?></option>
          <?php endforeach;?>
        </select>
      </div>
      <div class="flex flex-col md:flex-row items-center gap-2">
        <label for="staff" class="text-cyan-700 font-semibold">Staff:</label>
        <select name="staff" id="staff" class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono">
          <option value="">All Staff</option>
          <?php foreach($staffList as $s): ?>
            <option <?= $staff==$s['id']?'selected':''?> value="<?=$s['id']?>"><?=htmlspecialchars($s['name'])?></option>
          <?php endforeach;?>
        </select>
      </div>
    </div>
    <div class="flex items-center w-full md:w-auto">
      <button type="submit"
        class="px-6 py-2 bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest w-full md:w-auto">
        Filter
      </button>
    </div>
  </form>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-cyan-50 border border-cyan-100 rounded-xl p-6 flex flex-col items-center shadow font-mono">
      <span class="text-2xl font-bold text-cyan-700"><?=$total?></span>
      <span class="text-cyan-500">Total Projects</span>
    </div>
    <div class="bg-cyan-50 border border-cyan-100 rounded-xl p-6 shadow">
      <canvas id="statusChart"></canvas>
    </div>
    <div class="bg-cyan-50 border border-cyan-100 rounded-xl p-6 shadow">
      <canvas id="staffChart"></canvas>
    </div>
    <div class="bg-cyan-50 border border-cyan-100 rounded-xl p-6 shadow">
      <canvas id="uncompletedProjectTimelineChart" class="w-full max-w-4xl mx-auto"></canvas>
    </div>
  </div>

  <div class="overflow-x-auto rounded-xl shadow-inner">
    <table id="projectTable" class="w-full border border-cyan-100 bg-white bg-opacity-90 font-mono text-cyan-900">
      <thead>
        <tr class="bg-cyan-50 text-cyan-700 text-left">
          <th class="p-3 font-bold">#</th>
          <th class="p-3 font-bold">Title</th>
          <th class="p-3 font-bold">Status</th>
          <th class="p-3 font-bold">Main Status</th>
          <th class="p-3 font-bold">Staff</th>
          <th class="p-3 font-bold">Deadline</th>
          <th class="p-3 font-bold">Remark</th>
          <th class="p-3 font-bold">Created</th>
          <th class="p-3 font-bold">Time Taken to Complete</th>
          <th class="p-3 font-bold">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $rowsPerPage = 10;
        $totalPages = ceil(count($projects) / $rowsPerPage);
        $currentPage = (int) ($_GET['page'] ?? 1);
        $offset = ($currentPage - 1) * $rowsPerPage;
        $projects = array_slice($projects, $offset, $rowsPerPage);
        foreach($projects as $i=>$p): ?>
        <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition">
          <td class="p-3"><?= $i+1 + $offset?></td>
          <td class="p-3"><?= htmlspecialchars($p['title']) ?></td>
          <td class="p-3 whitespace-nowrap">
            <span class="<?=
              $p['status'] === 'confirmed fixed' ? 'bg-green-400 text-white px-2 rounded-lg' :
              ($p['status'] === 'fixed' ? 'bg-green-200 text-green-900 px-2 rounded-lg' :
              ($p['status'] === 'needs redo' ? 'bg-red-400 text-white px-2 rounded-lg' :
              ($p['status'] === 'in progress' ? 'bg-yellow-300 text-yellow-900 px-2 rounded-lg' :
              ($p['status'] === 'assigned' ? 'bg-cyan-300 text-cyan-900 px-2 rounded-lg' :
              'bg-gray-200 text-gray-700 px-2 rounded-lg'))))
            ?>">
              <?= ucfirst($p['status']) ?>
            </span>
          </td>
          <td class="p-3 whitespace-nowrap">
            <span class="<?=
              $p['main_status'] === 'completed' ? 'bg-green-400 text-white px-2 rounded-lg' :
              ($p['main_status'] === 'under_process' ? 'bg-yellow-200 text-yellow-900 px-2 rounded-lg' :
              ($p['main_status'] === 'needs_attention' ? 'bg-red-400 text-white px-2 rounded-lg' :
              'bg-gray-200 text-gray-700 px-2 rounded-lg'))
            ?>">
              <?= ucfirst($p['main_status']) ?>
            </span>
          </td>
          <td class="p-3 whitespace-nowrap"><?= htmlspecialchars($p['staff_name'] ?: 'Unassigned') ?></td>
          <td class="p-3 whitespace-nowrap">
            <?php
            $deadline_date = $p['deadline_date'];
            if ($deadline_date) {
              $diff = strtotime($deadline_date) - time();
              $days = floor($diff / (60 * 60 * 24));
              $warning = null;
              if ($days < 0) {
                $warning = 'bg-red-300 text-red-900 animate-pulse';
              } elseif ($days < 3) {
                $warning = 'bg-yellow-100 text-yellow-700';
              } else {
                $warning = 'bg-green-100 text-green-700';
              }
            ?>
            <span class="px-2 py-1 whitespace-nowrap rounded <?= $warning ?? '' ?>"><?= htmlspecialchars($deadline_date) ?></span>
            <?php } else { ?>
            <i>-</i>
            <?php } ?>
          </td>
          <td class="p-3"><?= htmlspecialchars($p['remark'] ?: '-') ?></td>
          <td class="p-3 whitespace-nowrap"><?= date('Y-m-d',strtotime($p['created_at'])) ?></td>
          <td class="p-3">
            <?php
            if ($p['status'] === 'fixed' || $p['status'] === 'confirmed fixed') {
              $assignedDate = new DateTime($p['assigned_at']);
              $completionDate = new DateTime($p['completion_date']);
              $interval = $completionDate->diff($assignedDate);
              echo $interval->format('%a days %h hours %i minutes');
            } else {
              echo '-';
            }
            ?>
          </td>
          <td class="p-3">
            <a href="project_detail.php?id=<?=$p['id']?>" class="bg-cyan-400 hover:bg-cyan-500 text-white font-bold px-3 py-1 rounded-lg shadow transition">View</a>
          </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>

  <div class="flex items-center justify-center my-4 font-mono">
    <nav class="rounded-lg px-4 py-2 flex space-x-2">
      <?php if ($currentPage > 1): ?>
      <a href="?page=<?= $currentPage - 1 ?>" class="bg-cyan-400 hover:bg-cyan-500 text-white font-bold px-3 py-1 rounded-lg transition">Previous</a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <?php if ($i == $currentPage): ?>
      <span class="bg-cyan-600 text-white font-bold px-3 py-1 rounded-lg transition"><?=$i?></span>
      <?php else: ?>
      <a href="?page=<?= $i ?>" class="bg-cyan-400 hover:bg-cyan-500 text-white font-bold px-3 py-1 rounded-lg transition"><?=$i?></a>
      <?php endif; ?>
      <?php endfor; ?>

      <?php if ($currentPage < $totalPages): ?>
      <a href="?page=<?= $currentPage + 1 ?>" class="bg-cyan-400 hover:bg-cyan-500 text-white font-bold px-3 py-1 rounded-lg transition">Next</a>
      <?php endif; ?>
    </nav>
  </div>

  <div id="summaryTable">
    <!-- Add summary of the following: 1. amount of projects -->
    <div class="mt-6">
      <h3 class="text-lg font-bold text-cyan-700">Project Summary</h3>
      <p class="text-cyan-600">Total Projects: <?= count($projects) ?></p>
    </div>
    <!-- amount of completed projects -->
    <div class="mt-2">
      <h4 class="text-md font-bold text-cyan-700">Completed Projects</h4>
      <p class="text-cyan-600">Total Completed: <?= $completedProjectsCount ?></p>
    </div>
    <!-- amount of uncompleted projects -->
    <div class="mt-2">
      <h4 class="text-md font-bold text-cyan-700">Uncompleted Projects</h4>
      <p class="text-cyan-600">Total Uncompleted: <?= $uncompletedProjectsCount ?></p>
    </div>
    <!-- amount of projects by status -->
    <div class="mt-2">
      <h4 class="text-md font-bold text-cyan-700">Projects by Status</h4>
      <ul class="list-disc pl-6 text-cyan-600">
        <?php foreach($byStatus as $status => $count): ?>
          <li><?= ucfirst($status) ?>: <?= $count ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <!-- amount of time spent on projects -->
    <div class="mt-2">
      <h4 class="text-md font-bold text-cyan-700">Time Spent on Projects</h4>
      <p class="text-cyan-600">Total Time: <?= $totalTimeSpent ?> hours</p>
    </div>
  </div>

<!-- export script logic -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  document.body.insertAdjacentHTML("beforeend", `
    <button id="exportPdfBtn" class="fixed bottom-4 right-4 bg-cyan-500 hover:bg-cyan-600 text-white font-bold px-4 py-2 rounded-lg shadow">
      Export Projects to PDF
    </button>
  `);

  document.getElementById("exportPdfBtn").addEventListener("click", () => {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("l", "pt", "a4");

    doc.setFont("courier", "bold"); // font-mono equivalent
    doc.setFontSize(18);
    doc.setTextColor("#0891b2"); // cyan-700
    doc.text("Project List", 40, 40);

    doc.autoTable({
      html: '#projectTable',
      startY: 60,
      headStyles: {
        fillColor: [8, 145, 178],   // cyan-700
        textColor: [236, 254, 255], // cyan-50
        fontStyle: 'bold',
        font: 'courier'
      },
      bodyStyles: {
        font: 'courier',
        textColor: [22, 78, 99],    // cyan-900
      },
      styles: {
        fontSize: 9,
        cellPadding: 5,
        overflow: 'linebreak',
      },
      theme: 'striped',
      didParseCell: function (data) {
        // Always extract plain text from any HTML node
        if (data.cell && data.cell.raw) {
          let cellContent = data.cell.raw;
          let text = "";
          if (typeof cellContent === "string") {
            text = cellContent;
          } else if (cellContent instanceof HTMLElement) {
            text = cellContent.textContent || "";
          }
          data.cell.text = text.trim();
        }
      }
    });

    let finalY = doc.lastAutoTable.finalY + 25;

    // Add Summary
    doc.setFontSize(13);
    doc.setTextColor("#0891b2");
    doc.text("Project Summary", 40, finalY);

    doc.setFont("courier", "normal");
    doc.setFontSize(11);
    doc.setTextColor("#155e75");

    let summaryY = finalY + 16;
    const summary = document.querySelector("#summaryTable");
    const lines = [];

    if (summary) {
      summary.querySelectorAll("h3, h4, p, li").forEach(el => {
        const text = el.textContent?.trim();
        if (text) lines.push(text);
      });
    }

    lines.forEach(line => {
      doc.text(line, 40, summaryY);
      summaryY += 14;
    });

    doc.save("Projects.pdf");
  });
});
</script>

<script>
// Charts
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
  type: 'pie',
  data: {
    labels: <?=json_encode(array_keys($byStatus))?>,
    datasets: [{
      data: <?=json_encode(array_values($byStatus))?>,
      backgroundColor: ['#22d3ee','#f87171','#fbbf24','#34d399','#a78bfa','#4ade80']
    }]
  },
  options: {
    plugins: {
      legend: { labels: { font: { family: 'monospace', size: 14 } } }
    }
  }
});

const staffCtx = document.getElementById('staffChart').getContext('2d');
new Chart(staffCtx, {
  type: 'bar',
  data: {
    labels: <?=json_encode(array_keys($byStaff))?>,
    datasets: [{
      data: <?=json_encode(array_values($byStaff))?>,
      backgroundColor: '#22d3ee'
    }]
  },
  options: {
    indexAxis: 'y',
    plugins: {
      legend: { display: false },
      tooltip: { enabled: true }
    },
    scales: {
      x: { ticks: { font: { family: 'monospace', size: 13 } } },
      y: { ticks: { font: { family: 'monospace', size: 13 } } }
    }
  }
});

// chart bar that shows uncompleted project timeline where Y axis is projects and x is weeks
const uncompletedProjectTimelineCtx = document.getElementById('uncompletedProjectTimelineChart').getContext('2d');
new Chart(uncompletedProjectTimelineCtx, {
  type: 'bar',
  data: {
    labels: <?=json_encode(array_keys($uncompletedProjectsByWeek))?>,
    datasets: [{
      data: <?=json_encode(array_values($uncompletedProjectsByWeek))?>,
      backgroundColor: '#f87171' // use a different color to distinguish from the existing chart
    }]
  },
  options: {
    indexAxis: 'y',
    plugins: {
      legend: { display: false },
      tooltip: { enabled: true }
    },
    scales: {
      x: { ticks: { font: { family: 'monospace', size: 13 } } },
      y: { ticks: { font: { family: 'monospace', size: 13 } } }
    }
  }
});

</script>
</body>
</html>
