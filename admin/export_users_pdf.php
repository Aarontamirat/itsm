<?php
require_once('../libs/tcpdf/tcpdf.php');
require '../config/db.php';

// Get filters from query string
$branchFilter = $_GET['branch_id'] ?? '';
$roleFilter = $_GET['role'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$nameFilter = $_GET['name'] ?? '';

// Build dynamic WHERE clause
$where = [];
$params = [];

if ($branchFilter !== '') {
    $where[] = 'u.branch_id = ?';
    $params[] = $branchFilter;
}
if ($roleFilter !== '') {
    $where[] = 'u.role = ?';
    $params[] = $roleFilter;
}
if ($statusFilter !== '') {
    $where[] = 'u.is_active = ?';
    $params[] = $statusFilter;
}
if ($nameFilter !== '') {
    $where[] = 'u.name LIKE ?';
    $params[] = '%' . $nameFilter . '%';
}

$sql = "SELECT u.*, b.name AS branch_name FROM users u
        LEFT JOIN branches b ON u.branch_id = b.id";
if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

$html = '<h2>User List</h2>';
$html .= '<table border="1" cellpadding="4">
<thead>
<tr style="background-color:#f2f2f2;">
  <th>ID</th>
  <th>Name</th>
  <th>Email</th>
  <th>Role</th>
  <th>Status</th>
  <th>Branch</th>
</tr>
</thead>
<tbody>';

foreach ($stmt as $row) {
    $html .= '<tr>
        <td>' . $row['id'] . '</td>
        <td>' . htmlspecialchars($row['name']) . '</td>
        <td>' . htmlspecialchars($row['email']) . '</td>
        <td>' . htmlspecialchars($row['role']) . '</td>
        <td>' . htmlspecialchars($row['is_active']) . '</td>
        <td>' . ($row['branch_name'] ?? 'N/A') . '</td>
      </tr>';
}

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('users.pdf', 'D');
