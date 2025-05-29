<?php
session_start();
include '../config/db.php';

$where = 'WHERE 1=1';
$params = [];

if (!empty($_GET['branch'])) {
    $where .= ' AND i.branch_id = ?';
    $params[] = $_GET['branch'];
}
if (!empty($_GET['status'])) {
    $where .= ' AND i.status = ?';
    $params[] = $_GET['status'];
}

$sql = "SELECT 
    i.id AS incident_id,
    i.title AS incident_title,
    i.status,
    i.priority,
    i.created_at,
    i.assigned_date,
    i.fixed_date,
    u.name AS reported_by,
    s.name AS assigned_to,
    b.name AS branch_name,
    c.category_name
FROM incidents i
LEFT JOIN users u ON i.created_by = u.id
LEFT JOIN users s ON i.assigned_to = s.id
LEFT JOIN branches b ON i.branch_id = b.id
LEFT JOIN incident_categories c ON i.category_id = c.id
$where
ORDER BY i.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Knowledge Base</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body class="bg-gray-100">

<?php include '../includes/sidebar.php'; ?>
<?php include '../header.php'; ?>

<div class="p-6 bg-gray-900 mt-6 text-white">
  <h1 class="text-2xl font-bold mb-4">Comprehensive Reports</h1>

  <form method="get" class="flex gap-4 mb-6">
    <select name="branch" class="p-2 bg-gray-800 rounded">
      <option value="">All Branches</option>
      <?php foreach ($branches as $b): ?>
        <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
      <?php endforeach; ?>
    </select>
    <select name="status" class="p-2 bg-gray-800 rounded">
      <option value="">All Statuses</option>
      <option value="pending">Pending</option>
      <option value="fixed">Fixed</option>
      <option value="not-fixed">Not Fixed</option>
    </select>
    <button type="submit" class="px-4 py-2 bg-blue-600 rounded hover:bg-blue-700">Filter</button>
    <a href="export_reports.php?format=pdf" class="px-4 py-2 bg-red-600 rounded hover:bg-red-700">Export PDF</a>
    <a href="export_reports.php?format=csv" class="px-4 py-2 bg-green-600 rounded hover:bg-green-700">Export CSV</a>
  </form>

  <div class="overflow-auto">
    <table class="min-w-full table-auto border-collapse border border-gray-700">
      <thead class="bg-gray-800">
        <tr>
          <th class="border p-2">Incident ID</th>
          <th class="border p-2">Title</th>
          <th class="border p-2">Status</th>
          <th class="border p-2">Priority</th>
          <th class="border p-2">Reported By</th>
          <th class="border p-2">Assigned To</th>
          <th class="border p-2">Branch</th>
          <th class="border p-2">Category</th>
          <th class="border p-2">Created At</th>
          <th class="border p-2">Assigned Date</th>
          <th class="border p-2">Fixed Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reports as $r): ?>
          <tr>
            <td class="border p-2"><?= $r['incident_id'] ?></td>
            <td class="border p-2"><?= htmlspecialchars($r['incident_title']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($r['status']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($r['priority']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($r['reported_by']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($r['assigned_to']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($r['branch_name']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($r['category_name']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($r['created_at']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($r['assigned_date']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($r['fixed_date']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>



</body>
</html>
