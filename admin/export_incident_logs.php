<?php
require '../config/db.php';
require_once '../libs/tcpdf/tcpdf.php';

$format      = $_GET['format'] ?? 'csv';
$incident_id = $_GET['incident_id'] ?? null;
$user_id     = $_GET['user_id'] ?? null;
$from        = $_GET['from'] ?? null;
$to          = $_GET['to'] ?? null;

if (!in_array($format, ['csv', 'pdf'])) {
    die('Invalid format.');
}

// Build dynamic filters
$where = [];
$params = [];

if ($incident_id) {
    $where[] = 'logs.incident_id = ?';
    $params[] = $incident_id;
}
if ($user_id) {
    $where[] = 'logs.user_id = ?';
    $params[] = $user_id;
}
if ($from && $to) {
    $where[] = 'DATE(logs.created_at) BETWEEN ? AND ?';
    $params[] = $from;
    $params[] = $to;
} elseif ($from) {
    $where[] = 'DATE(logs.created_at) >= ?';
    $params[] = $from;
} elseif ($to) {
    $where[] = 'DATE(logs.created_at) <= ?';
    $params[] = $to;
}

$filterSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Fetch data
$sql = "
    SELECT logs.created_at, users.name, logs.action 
    FROM incident_logs logs
    JOIN users ON users.id = logs.user_id
    $filterSql
    ORDER BY logs.created_at DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Export CSV
if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="incident_logs.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Date', 'User', 'Action']);
    foreach ($logs as $log) {
        fputcsv($output, [$log['created_at'], $log['name'], $log['action']]);
    }
    fclose($output);
    exit;
}

// Export PDF with TCPDF
if ($format === 'pdf') {
    $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('Incident Logs Report');
    $pdf->SetHeaderData('', 0, 'Incident Logs Report', 'Generated: ' . date('Y-m-d H:i'));
    $pdf->setHeaderFont(['helvetica', '', 12]);
    $pdf->setFooterFont(['helvetica', '', 10]);
    $pdf->SetMargins(10, 25, 10);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->AddPage();

    // Build HTML Table
    $html = '<table border="1" cellpadding="5">
                <thead>
                    <tr style="background-color:#f2f2f2;">
                        <th width="30%">Date</th>
                        <th width="30%">User</th>
                        <th width="40%">Action</th>
                    </tr>
                </thead><tbody>';
    foreach ($logs as $log) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($log['created_at']) . '</td>
                    <td>' . htmlspecialchars($log['name']) . '</td>
                    <td>' . htmlspecialchars($log['action']) . '</td>
                  </tr>';
    }
    $html .= '</tbody></table>';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('incident_logs.pdf', 'D');
    exit;
}
