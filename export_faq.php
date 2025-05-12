<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['role'])) {
    die("Access denied.");
}

$format = $_GET['format'] ?? 'csv';
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$params = ["%$search%", "%$search%", "%$search%"];
$query = "SELECT f.*, u.name AS author 
          FROM faqs f 
          JOIN users u ON f.created_by = u.id 
          WHERE (f.title LIKE ? OR f.category LIKE ? OR f.solution LIKE ?)";
if (!empty($category)) {
    $query .= " AND f.category = ?";
    $params[] = $category;
}
$query .= " ORDER BY f.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="faq_export.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Title', 'Category', 'Solution', 'Author', 'Linked Incident']);
    foreach ($faqs as $faq) {
        fputcsv($output, [
            $faq['title'],
            $faq['category'],
            strip_tags($faq['solution']),
            $faq['author'],
            $faq['linked_incident']
        ]);
    }
    fclose($output);
    exit;
}

if ($format === 'pdf') {
    require_once('libs/tcpdf/tcpdf.php');

    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('IT Support System');
    $pdf->SetAuthor('System');
    $pdf->SetTitle('FAQ Export');
    $pdf->SetHeaderData('', 0, 'FAQ Knowledge Base Export', '');
    $pdf->setHeaderFont(Array('helvetica', '', 10));
    $pdf->setFooterFont(Array('helvetica', '', 8));
    $pdf->SetMargins(10, 20, 10);
    $pdf->SetAutoPageBreak(TRUE, 10);
    $pdf->AddPage();

    $html = '<h2>FAQ Knowledge Base Export</h2>';
    $html .= '<table border="1" cellpadding="4" cellspacing="0">';
    $html .= '<thead>
        <tr style="background-color:#f0f0f0;">
            <th><strong>Title</strong></th>
            <th><strong>Category</strong></th>
            <th><strong>Solution</strong></th>
            <th><strong>Author</strong></th>
            <th><strong>Incident Link</strong></th>
        </tr>
    </thead><tbody>';

    foreach ($faqs as $faq) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($faq['title']) . '</td>';
        $html .= '<td>' . htmlspecialchars($faq['category']) . '</td>';
        $html .= '<td>' . nl2br(htmlspecialchars($faq['solution'])) . '</td>';
        $html .= '<td>' . htmlspecialchars($faq['author']) . '</td>';
        $html .= '<td>' . ($faq['linked_incident'] ?: '-') . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('faq_export.pdf', 'D');
    exit;
}
?>
