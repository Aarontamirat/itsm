<?php
require_once('../libs/tcpdf/tcpdf.php');
require '../config/db.php';

$branchFilter = $_GET['branch_id'] ?? '';

if ($branchFilter) {
    $stmt = $pdo->prepare("SELECT u.*, b.name AS branch_name FROM users u
        LEFT JOIN branches b ON u.branch_id = b.id
        WHERE u.branch_id = ?");
    $stmt->execute([$branchFilter]);
} else {
    $stmt = $pdo->query("SELECT u.*, b.name AS branch_name FROM users u
        LEFT JOIN branches b ON u.branch_id = b.id");
}

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
  <th>Branch</th>
</tr>
</thead>
<tbody>';

foreach ($stmt as $row) {
    $html .= '<tr>
        <td>' . $row['id'] . '</td>
        <td>' . htmlspecialchars($row['name']) . '</td>
        <td>' . htmlspecialchars($row['email']) . '</td>
        <td>' . $row['role'] . '</td>
        <td>' . ($row['branch_name'] ?? 'N/A') . '</td>
      </tr>';
}

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('users.pdf', 'D');
