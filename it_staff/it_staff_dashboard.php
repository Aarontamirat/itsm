<!-- Include sidebar.php with limited links -->
<?php 
    session_start();
    require_once '../config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>IT Staff Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-black">

  <?php include '../includes/sidebar.php'; ?>
    <?php include '../header.php'; ?>

    <?php

    // Fetch counts for cards
    $openIncidents = $resolvedIncidents = $pendingIncidents = 0;
    $statusMap = [
      'Open' => ['not fixed', 'assigned', 'support'],
      'Resolved' => ['fixed'],
      'Pending' => ['pending']
    ];

    // Open Incidents
    $stmt = $pdo->prepare("SELECT 
      SUM(status IN ('not fixed', 'assigned', 'support')) AS open_count, 
      SUM(status = 'fixed') AS resolved_count, 
      SUM(status = 'pending') AS pending_count 
      FROM incidents
      WHERE assigned_to = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $openIncidents = $row['open_count'] ?? 0;
    $resolvedIncidents = $row['resolved_count'] ?? 0;
    $pendingIncidents = $row['pending_count'] ?? 0;

    // Fetch incidents by status for bar chart (using status as category)
    $categories = [];
    $categoryCounts = [];
    $catStmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM incidents WHERE assigned_to = :user_id GROUP BY status");
    $catStmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $catStmt->execute();
    while ($catRow = $catStmt->fetch(PDO::FETCH_ASSOC)) {
      $categories[] = ucfirst($catRow['status']);
      $categoryCounts[] = $catRow['count'];
    }

    // Fetch recent activity (last 3 incidents)
    $recentIncidents = [];
    $recentStmt = $pdo->prepare("SELECT created_at, title, status, assigned_to FROM incidents WHERE assigned_to = :user_id ORDER BY created_at DESC LIMIT 3");
    $recentStmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $recentStmt->execute();
    while ($recentRow = $recentStmt->fetch(PDO::FETCH_ASSOC)) {
      // Get assigned_to name from users table
      $assignedName = '';
      if (!empty($recentRow['assigned_to'])) {
        $userStmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
        $userStmt->execute([$recentRow['assigned_to']]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);
        $assignedName = $user['name'] ?? '';
      }
      $recentRow['assigned_to'] = $assignedName;
      $recentIncidents[] = $recentRow;
    }
    ?>

    <main class="ml-64 px-0 py-0">
      <div class="max-w-6xl mx-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 pt-20 fade-in tech-border glow mt-8">
      <h1 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">IT Staff Dashboard</h1>
      <p class="text-center text-cyan-500 mb-8 font-mono">Overview of IT support activity and incident status</p>

      <!-- Cards Section -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
      <!-- Card 1: Open Incidents -->
      <div class="bg-cyan-50 bg-opacity-90 rounded-xl shadow-lg p-6 flex flex-col items-center border-t-4 border-cyan-400 hover:scale-105 transition-transform duration-200">
        <svg class="w-12 h-12 text-cyan-500 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M9 17v-2a4 4 0 0 1 8 0v2"></path>
        <circle cx="12" cy="7" r="4"></circle>
        <rect x="2" y="17" width="20" height="5" rx="2"></rect>
        </svg>
        <div class="text-3xl font-extrabold font-mono text-cyan-700"><?php echo $openIncidents; ?></div>
        <div class="text-cyan-500 mt-2 font-mono">Open Incidents</div>
      </div>
      <!-- Card 2: Incidents Resolved -->
      <div class="bg-green-50 bg-opacity-90 rounded-xl shadow-lg p-6 flex flex-col items-center border-t-4 border-green-400 hover:scale-105 transition-transform duration-200">
        <svg class="w-12 h-12 text-green-500 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M5 13l4 4L19 7"></path>
        </svg>
        <div class="text-3xl font-extrabold font-mono text-green-700"><?php echo $resolvedIncidents; ?></div>
        <div class="text-green-500 mt-2 font-mono">Incidents Resolved</div>
      </div>
      <!-- Card 3: Pending Incidents -->
      <div class="bg-yellow-50 bg-opacity-90 rounded-xl shadow-lg p-6 flex flex-col items-center border-t-4 border-yellow-400 hover:scale-105 transition-transform duration-200">
        <svg class="w-12 h-12 text-yellow-500 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M12 8v4l3 3"></path>
        <circle cx="12" cy="12" r="10"></circle>
        </svg>
        <div class="text-3xl font-extrabold font-mono text-yellow-700"><?php echo $pendingIncidents; ?></div>
        <div class="text-yellow-500 mt-2 font-mono">Pending Incidents</div>
      </div>
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
      <!-- Pie Chart Card -->
      <div class="bg-white bg-opacity-90 rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2 text-cyan-700 font-mono">
        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10"></circle>
          <path d="M12 2a10 10 0 0 1 10 10h-10z"></path>
        </svg>
        Incident Status Overview
        </h2>
        <canvas id="pieChart" height="180"></canvas>
      </div>
      <!-- Bar Chart Card -->
      <div class="bg-white bg-opacity-90 rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2 text-cyan-700 font-mono">
        <svg class="w-6 h-6 text-cyan-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <rect x="3" y="12" width="4" height="8"></rect>
          <rect x="9" y="8" width="4" height="12"></rect>
          <rect x="15" y="4" width="4" height="16"></rect>
        </svg>
        Incidents by Status
        </h2>
        <canvas id="barChart" height="180"></canvas>
      </div>
      </div>

      <!-- Recent Activity Table -->
      <div class="bg-white bg-opacity-90 rounded-xl shadow-lg p-6">
      <h2 class="text-xl font-semibold mb-4 flex items-center gap-2 text-cyan-700 font-mono">
        <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7"></path>
        <path d="M16 3v4"></path>
        <path d="M8 3v4"></path>
        <rect x="3" y="7" width="18" height="13" rx="2"></rect>
        </svg>
        Recent Activity
      </h2>
      <div class="overflow-x-auto rounded-xl shadow-inner">
        <table class="min-w-full border border-cyan-100 bg-white bg-opacity-90 font-mono text-cyan-900">
        <thead>
          <tr class="bg-cyan-50 text-cyan-700 text-left">
          <th class="py-2 px-4 font-bold">Date</th>
          <th class="py-2 px-4 font-bold">Incident</th>
          <th class="py-2 px-4 font-bold">Status</th>
          <th class="py-2 px-4 font-bold">Assigned To</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentIncidents as $incident): ?>
          <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition">
            <td class="py-2 px-4"><?php echo htmlspecialchars(date('Y-m-d', strtotime($incident['created_at']))); ?></td>
            <td class="py-2 px-4"><?php echo htmlspecialchars($incident['title']); ?></td>
            <td class="py-2 px-4">
            <?php
            $status = $incident['status'];
            $statusClass = [
              'not fixed' => 'bg-blue-100 text-blue-700',
              'assigned' => 'bg-blue-100 text-blue-700',
              'support' => 'bg-blue-100 text-blue-700',
              'fixed' => 'bg-green-100 text-green-700',
              'pending' => 'bg-yellow-100 text-yellow-700',
              'rejected' => 'bg-red-100 text-red-700'
            ];
            ?>
            <span class="<?php echo $statusClass[$status] ?? 'bg-gray-100 text-gray-700'; ?> px-2 py-1 rounded">
              <?php echo htmlspecialchars(ucfirst($status)); ?>
            </span>
            </td>
            <td class="py-2 px-4"><?php echo htmlspecialchars($incident['assigned_to']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        </table>
      </div>
      </div>
      </div>
    </main>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      // Pie Chart
      new Chart(document.getElementById('pieChart'), {
      type: 'doughnut',
      data: {
        labels: ['Open', 'Resolved', 'Pending'],
        datasets: [{
        data: [
          <?php echo $openIncidents; ?>,
          <?php echo $resolvedIncidents; ?>,
          <?php echo $pendingIncidents; ?>
        ],
        backgroundColor: ['#3b82f6', '#22c55e', '#facc15'],
        borderWidth: 2
        }]
      },
      options: {
        plugins: {
        legend: { display: true, position: 'bottom' }
        }
      }
      });

      // Bar Chart
      new Chart(document.getElementById('barChart'), {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($categories); ?>,
        datasets: [{
        label: 'Incidents',
        data: <?php echo json_encode($categoryCounts); ?>,
        backgroundColor: ['#6366f1', '#06b6d4', '#f59e42', '#a78bfa', '#f87171', '#34d399', '#fbbf24']
        }]
      },
      options: {
        plugins: {
        legend: { display: false }
        },
        scales: {
        y: { beginAtZero: true }
        }
      }
      });
    </script>

</body>
</html>
