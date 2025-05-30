<?php
session_start();
require '../config/db.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Incident Reports</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<?php
require_once '../includes/sidebar.php';
require_once '../header.php';
?>

  <div class="max-w-7xl ms-auto p-6 mt-4">
    <h1 class="text-3xl font-bold mb-6">📊 Incident Reports</h1>

    <!-- Filters -->
    <div class="flex flex-wrap gap-4 mb-6">
      <select id="branchFilter" class="p-2 border rounded">
        <option value="">All Branches</option>
        <?php
        // Fetch branches from the database
        $stmt = $pdo->prepare("SELECT id, name FROM branches ORDER BY name ASC");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . '</option>';
        }
        ?>
      </select>
      <select id="categoryFilter" class="p-2 border rounded">
        <option value="">All Categories</option>
        <?php
        // Fetch categories from the database
        $stmt = $pdo->prepare("SELECT id, name FROM kb_categories ORDER BY name ASC");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . '</option>';
        }
        ?>
      </select>
      <input type="date" id="fromDate" class="p-2 border rounded">
      <input type="date" id="toDate" class="p-2 border rounded">
      <button id="filterBtn" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <canvas id="incidentCountChart"></canvas>
      <canvas id="staffPerformanceChart"></canvas>
    </div>

    <!-- Table -->
    <div class="bg-white p-4 rounded shadow">
      <div class="flex justify-between mb-4">
        <h2 class="text-xl font-semibold">Incident Report Details</h2>
        <div class="flex gap-2">
          <button onclick="exportCSV()" class="bg-green-500 text-white px-4 py-2 rounded">Export CSV</button>
          <button onclick="exportPDF()" class="bg-red-500 text-white px-4 py-2 rounded">Export PDF</button>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table id="reportTable" class="min-w-full table-auto">
          <thead>
            <tr class="bg-gray-200">
              <th class="px-4 py-2">Incident</th>
              <th>Branch</th>
              <th>Category</th>
              <th>Reported</th>
              <th>Fixed</th>
              <th>Days to Fix</th>
              <th>Assigned To</th>
            </tr>
          </thead>
          <tbody>
            <!-- PHP Loop: Fetch report data -->
          </tbody>
        </table>
      </div>
    </div>
  </div>


  <?php
             $branch ='Kazanchis Branch';
             $sql = "SELECT * FROM incident_fix_times WHERE 1=1";
// if ($branch) $sql .= " AND branch_name = '$branch'";
// if ($category) $sql .= " AND name = '$category'";
// if ($from && $to) $sql .= " AND report_date BETWEEN '$from' AND '$to'";

$result = $pdo->prepare($sql);

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
  echo '<p>' . $row['title'] .'</p>';
}
             ?>

  <script src="report-logic.js"></script>


</body>
</html>
