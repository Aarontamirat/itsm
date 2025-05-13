<?php
require '../config/db.php'; // your PDO connection file
session_start();

$incident_id = $_GET['id'] ?? null;
if (!$incident_id) {
    echo "Incident ID not provided.";
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
</head>
<body class="p-6 bg-gray-100">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-semibold">Incident Log History</h2>
    <div class="flex gap-2">
        <?php
// Fetch users and incidents for dropdowns
$userStmt = $pdo->query("SELECT id, name FROM users ORDER BY name ASC");
$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

$incidentStmt = $pdo->query("SELECT id, title FROM incidents ORDER BY id DESC");
$incidents = $incidentStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<form method="GET" action="export_incident_logs.php" class="mb-6 p-4 border rounded bg-white shadow-md">
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

    <!-- Select User -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Filter by User</label>
      <select name="user_id" class="w-full border-gray-300 rounded mt-1">
        <option value="">All Users</option>
        <?php foreach ($users as $user): ?>
          <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Select Incident -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Filter by Incident</label>
      <select name="incident_id" class="w-full border-gray-300 rounded mt-1">
        <option value="">All Incidents</option>
        <?php foreach ($incidents as $incident): ?>
          <option value="<?= $incident['id'] ?>">#<?= $incident['id'] ?> - <?= htmlspecialchars($incident['title']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- From Date -->
    <div>
      <label class="block text-sm font-medium text-gray-700">From</label>
      <input type="date" name="from" class="w-full border-gray-300 rounded mt-1">
    </div>

    <!-- To Date -->
    <div>
      <label class="block text-sm font-medium text-gray-700">To</label>
      <input type="date" name="to" class="w-full border-gray-300 rounded mt-1">
    </div>

  </div>

  <!-- Export Buttons -->
  <div class="mt-4 flex gap-3">
    <button type="submit" name="format" value="csv"
      class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
      Export CSV
    </button>
    <button type="submit" name="format" value="pdf"
      class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
      Export PDF
    </button>
  </div>
</form>

    </div>
  </div>

  <div class="bg-white shadow rounded p-4">
    <?php if (count($logs) > 0): ?>
      <div class="overflow-x-auto">
        <table class="min-w-full border text-sm">
          <thead class="bg-gray-200">
            <tr>
              <th class="p-2 border">Date</th>
              <th class="p-2 border">User</th>
              <th class="p-2 border">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($logs as $log): ?>
              <tr>
                <td class="p-2 border"><?= htmlspecialchars($log['created_at']) ?></td>
                <td class="p-2 border"><?= htmlspecialchars($log['name']) ?></td>
                <td class="p-2 border"><?= htmlspecialchars($log['action']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="mt-4 flex justify-center space-x-2">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a href="?id=<?= $incident_id ?>&page=<?= $i ?>"
             class="px-3 py-1 border rounded <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-white' ?>">
            <?= $i ?>
          </a>
        <?php endfor; ?>
      </div>
    <?php else: ?>
      <p class="text-gray-500">No logs found for this incident.</p>
    <?php endif; ?>
  </div>
</body>
</html>
