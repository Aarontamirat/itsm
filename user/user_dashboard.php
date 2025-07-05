<!-- User Dashboard -->
<?php session_start(); 
if (!isset($pdo)) {
  require_once '../config/db.php';
}

// Use PDO for stats
if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit;
}

$user_id = $_SESSION['user_id'];
$statuses = [
  'pending' => 0,
  'assigned' => 0,
  'fixed' => 0,
  'fixed_confirmed' => 0,
  'not_fixed' => 0,
  'rejected' => 0
];

$sql = "SELECT status, COUNT(*) as count FROM incidents WHERE submitted_by = :user_id GROUP BY status";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {
  switch (strtolower($row['status'])) {
    case 'pending':
      $statuses['pending'] = $row['count'];
      break;
    case 'assigned':
      $statuses['assigned'] = $row['count'];
      break;
    case 'fixed':
      $statuses['fixed'] = $row['count'];
      break;
    case 'fixed_confirmed':
      $statuses['fixed_confirmed'] = $row['count'];
      break;
    case 'not fixed':
      $statuses['not_fixed'] = $row['count'];
      break;
    case 'rejected':
      $statuses['rejected'] = $row['count'];
      break;
  }
}

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit;
}

$user_id = $_SESSION['user_id'];
$statuses = [
  'pending' => 0,
  'assigned' => 0,
  'fixed' => 0,
  'fixed_confirmed' => 0,
  'not_fixed' => 0,
  'rejected' => 0
];

$sql = "SELECT status, COUNT(*) as count FROM incidents WHERE submitted_by = :user_id GROUP BY status";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {
  switch (strtolower($row['status'])) {
    case 'pending':
      $statuses['pending'] = $row['count'];
      break;
    case 'assigned':
      $statuses['assigned'] = $row['count'];
      break;
    case 'fixed':
      $statuses['fixed'] = $row['count'];
      break;
    case 'fixed_confirmed':
      $statuses['fixed_confirmed'] = $row['count'];
      break;
    case 'not fixed':
      $statuses['not_fixed'] = $row['count'];
      break;
    case 'rejected':
      $statuses['rejected'] = $row['count'];
      break;
    }
  }

  // Replace the hardcoded numbers in the HTML below with dynamic values
  ?>
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="bg-gray-50 dark:bg-gray-900 min-h-screen font-sans">
    <div class="flex">
    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen ml-64">
      <!-- Header -->
      <?php include '../header.php'; ?>

      <!-- Page Content -->
      <main class="flex-1 p-6 bg-gradient-to-br from-blue-50 via-white to-blue-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 transition-all">
        <div class="max-w-6xl mx-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 pt-20 fade-in tech-border glow mt-8">
          <!-- Welcome Text -->
          <h1 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">
        Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?>!
          </h1>
          <p class="text-center text-cyan-500 mb-8 font-mono">Your IT Support Ticket Overview</p>

          <!-- Status Cards -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-12">
            <!-- Pending Tickets -->
            <a href="my_incident_history.php?status=pending" class="bg-cyan-50 bg-opacity-80 rounded-xl shadow-lg p-6 border-t-4 border-yellow-500 flex flex-col items-center font-mono hover:scale-105 transition-transform duration-200">
              <span class="text-5xl font-extrabold text-yellow-500 mb-2"><?php echo $statuses['pending']; ?></span>
              <span class="text-lg font-semibold text-cyan-700">Pending</span>
            </a>
            <!-- Assigned Tickets -->
            <a href="my_incident_history.php?status=assigned" class="bg-cyan-50 bg-opacity-80 rounded-xl shadow-lg p-6 border-t-4 border-blue-500 flex flex-col items-center font-mono hover:scale-105 transition-transform duration-200">
              <span class="text-5xl font-extrabold text-blue-500 mb-2"><?php echo $statuses['assigned']; ?></span>
              <span class="text-lg font-semibold text-cyan-700">Assigned</span>
            </a>
            <!-- Fixed Tickets -->
            <a href="my_incident_history.php?status=fixed" class="bg-cyan-50 bg-opacity-80 rounded-xl shadow-lg p-6 border-t-4 border-green-500 flex flex-col items-center font-mono hover:scale-105 transition-transform duration-200">
              <span class="text-5xl font-extrabold text-green-500 mb-2"><?php echo $statuses['fixed']; ?></span>
              <span class="text-lg font-semibold text-cyan-700">Fixed</span>
            </a>
            <!-- Fixed Confirmed Tickets -->
            <a href="my_incident_history.php?status=fixed_confirmed" class="bg-cyan-50 bg-opacity-80 rounded-xl shadow-lg p-6 border-t-4 border-green-500 flex flex-col items-center font-mono hover:scale-105 transition-transform duration-200">
              <span class="text-5xl font-extrabold text-green-500 mb-2"><?php echo $statuses['fixed_confirmed']; ?></span>
              <span class="text-lg font-semibold text-cyan-700">Fixed Confirmed</span>
            </a>
            <!-- Not Fixed Tickets -->
            <a href="my_incident_history.php?status=not fixed" class="bg-cyan-50 bg-opacity-80 rounded-xl shadow-lg p-6 border-t-4 border-gray-400 flex flex-col items-center font-mono hover:scale-105 transition-transform duration-200">
              <span class="text-5xl font-extrabold text-gray-500 mb-2"><?php echo $statuses['not_fixed']; ?></span>
              <span class="text-lg font-semibold text-cyan-700">Not Fixed</span>
            </a>
            <!-- Rejected Tickets -->
            <a href="my_incident_history.php?status=rejected" class="bg-cyan-50 bg-opacity-80 rounded-xl shadow-lg p-6 border-t-4 border-red-500 flex flex-col items-center font-mono hover:scale-105 transition-transform duration-200">
              <span class="text-5xl font-extrabold text-red-500 mb-2"><?php echo $statuses['rejected']; ?></span>
              <span class="text-lg font-semibold text-cyan-700">Rejected</span>
            </a>
          </div>
        </div>
      </main>
  </body>
  </html>