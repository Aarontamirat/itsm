<?php
session_start();
require_once '../config/db.php';
require_once '../libs/tcpdf/tcpdf.php'; // Adjust path accordingly

$q = $_GET['q'] ?? '';
$category_id = $_GET['category_id'] ?? '';

$sql = "SELECT kb_articles.id, kb_articles.title, kb_articles.content, kb_categories.name AS category_name, kb_articles.created_at
        FROM kb_articles
        LEFT JOIN kb_categories ON kb_articles.category_id = kb_categories.id
        WHERE 1=1 ";
$params = [];
if ($q !== '') {
    $sql .= " AND (kb_articles.title LIKE ? OR kb_articles.content LIKE ?) ";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($category_id !== '' && is_numeric($category_id)) {
    $sql .= " AND kb_articles.category_id = ? ";
    $params[] = $category_id;
}
$sql .= " ORDER BY kb_articles.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

$html = '<h2>Knowledge Base Articles Export</h2><table border="1" cellpadding="4"><thead><tr><th>ID</th><th>Title</th><th>Content</th><th>Category</th><th>Created At</th></tr></thead><tbody>';

foreach ($articles as $row) {
    $html .= '<tr>';
    $html .= '<td>' . $row['id'] . '</td>';
    $html .= '<td>' . htmlspecialchars($row['title']) . '</td>';
    $html .= '<td>' . htmlspecialchars(substr($row['content'], 0, 100)) . '...</td>';
    $html .= '<td>' . htmlspecialchars($row['category_name']) . '</td>';
    $html .= '<td>' . $row['created_at'] . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output('kb_articles_export.pdf', 'D');
exit;
