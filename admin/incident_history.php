<?php
require '../config/db.php'; // your PDO connection file
session_start();

$incident_id = $_GET['id'] ?? null;

// if no id is provided, show all incidents
if (isset($_GET['id'])) {
    $incident_id = (int) $_GET['id'];
} else {
    // Redirect or show all incidents
    header("Location: incidents.php");
    exit;
}

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total logs
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM incident_logs WHERE incident_id = ?");
$countStmt->execute([$incident_id]);
$totalLogs = $countStmt->fetchColumn();
$totalPages = ceil($totalLogs / $limit);

// Fetch paginated logs
$stmt = $pdo->prepare("
    SELECT logs.*, users.name 
    FROM incident_logs logs
    JOIN users ON users.id = logs.user_id
    WHERE logs.incident_id = ?
    ORDER BY logs.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $incident_id, PDO::PARAM_INT);
$stmt->bindValue(2, $limit, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Incident History</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .fade-in { animation: fadeIn 0.7s; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .tech-border { border: 2px solid #22d3ee; }
    .glow { box-shadow: 0 0 24px 2px #67e8f9, 0 0 0 2px #a7f3d0; }
  </style>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- header and sidebar -->
      <?php include '../includes/sidebar.php'; ?>
  <div class="flex-1 ml-20">
    <?php include '../header.php'; ?>

  <div class="max-w-7xl ms-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 pt-20 fade-in tech-border glow mt-8">
    <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Incident Log History</h2>
    <p class="text-center text-cyan-500 mb-6 font-mono">View and export incident activity logs</p>

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

    <div class="w-full mb-8">
      <form method="GET" action="export_incident_logs.php"
        class="flex flex-wrap gap-4 items-end w-full bg-cyan-50 bg-opacity-70 rounded-xl p-4 shadow-inner border border-cyan-100">
        <?php
        // Fetch users and incidents for dropdowns
        $userStmt = $pdo->query("SELECT id, name FROM users ORDER BY name ASC");
        $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

        $incidentStmt = $pdo->query("SELECT id, title FROM incidents ORDER BY id DESC");
        $incidents = $incidentStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="flex flex-col min-w-[140px] flex-1 sm:flex-none">
          <label class="block text-xs font-mono text-cyan-700 mb-1">User</label>
          <select name="user_id"
            class="px-4 py-2 rounded-lg border border-cyan-200 bg-white text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none font-mono transition duration-200">
            <option value="">All Users</option>
            <?php foreach ($users as $user): ?>
              <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="flex flex-col min-w-[180px] flex-1 sm:flex-none">
          <label class="block text-xs font-mono text-cyan-700 mb-1">Incident</label>
          <select name="incident_id"
            class="px-4 py-2 rounded-lg border border-cyan-200 bg-white text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none font-mono transition duration-200">
            <option value="">All Incidents</option>
            <?php foreach ($incidents as $incident): ?>
              <option value="<?= $incident['id'] ?>">#<?= $incident['id'] ?> - <?= htmlspecialchars($incident['title']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="flex flex-col min-w-[120px] flex-1 sm:flex-none">
          <label class="block text-xs font-mono text-cyan-700 mb-1">From</label>
          <input type="date" name="from"
            class="px-4 py-2 rounded-lg border border-cyan-200 bg-white text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none font-mono transition duration-200">
        </div>
        <div class="flex flex-col min-w-[120px] flex-1 sm:flex-none">
          <label class="block text-xs font-mono text-cyan-700 mb-1">To</label>
          <input type="date" name="to"
            class="px-4 py-2 rounded-lg border border-cyan-200 bg-white text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none font-mono transition duration-200">
        </div>
        <div class="flex gap-2 mt-2 sm:mt-6 w-full sm:w-auto justify-end">
          <button type="submit" name="format" value="csv"
            class="px-4 py-2 bg-gradient-to-r from-cyan-400 to-cyan-600 hover:from-cyan-500 hover:to-cyan-700 text-white rounded-lg font-mono font-semibold shadow transition flex items-center gap-2 w-full sm:w-auto justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4 4 4-4m-4-5v9"/>
            </svg>
            Export CSV
          </button>
          <button type="submit" name="format" value="pdf"
            class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-600 hover:from-green-500 hover:to-green-700 text-white rounded-lg font-mono font-semibold shadow transition flex items-center gap-2 w-full sm:w-auto justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Export PDF
          </button>
        </div>
      </form>
    </div>

    <div class="overflow-x-auto rounded-xl shadow-inner">
      <table class="w-full border border-cyan-100 bg-white bg-opacity-90 font-mono text-cyan-900">
        <thead>
          <tr class="bg-cyan-50 text-cyan-700 text-left">
            <th class="p-3 font-bold">Date</th>
            <th class="p-3 font-bold">User</th>
            <th class="p-3 font-bold">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($logs) > 0): ?>
            <?php foreach ($logs as $log): ?>
              <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition">
                <td class="p-3"><?= htmlspecialchars($log['created_at']) ?></td>
                <td class="p-3"><?= htmlspecialchars($log['name']) ?></td>
                <td class="p-3"><?= htmlspecialchars($log['action']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="3" class="p-4 text-center text-cyan-400">No logs found for this incident.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-8">
      <nav class="flex justify-center">
        <ul class="flex space-x-2 font-mono">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li>
              <a href="?id=<?= $incident_id ?>&page=<?= $i ?>"
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
</body>
</html>
