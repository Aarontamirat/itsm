<?php
require '../config/db.php';

$branch = $_GET['branch'] ?? '';
$category = $_GET['category'] ?? '';
$from = $_GET['fromDate'] ?? '';
$to = $_GET['toDate'] ?? '';

$sql = "SELECT * FROM incident_fix_times WHERE 1=1";
if ($branch) $sql .= " AND branch_name = '$branch'";
if ($category) $sql .= " AND name = '$category'";
if ($from && $to) $sql .= " AND report_date BETWEEN '$from' AND '$to'";

$result = $pdo->prepare($sql);
$tableData = [];
$incidentLabels = [];
$incidentCounts = [];
$staffLabels = [];
$staffCounts = [];

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
  $tableData[] = $row;
  $incidentLabels[] = $row['report_date'];
  $incidentCounts[] = 1; // We'll aggregate this in Chart.js
}

$staffResult = $pdo->prepare("SELECT * FROM staff_performance");
while ($row = $staffResult->fetch(PDO::FETCH_ASSOC)) {
  $staffLabels[] = $row['name'];
  $staffCounts[] = $row['fixed_count'];
}

echo json_encode([
  'tableData' => $tableData,
  'chartData' => [
    'incident' => ['labels' => $incidentLabels, 'counts' => $incidentCounts],
    'staff' => ['labels' => $staffLabels, 'counts' => $staffCounts],
  ]
]);
?>
