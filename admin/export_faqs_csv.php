<?php
require '../config/db.php';
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=faqs.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Question', 'Answer', 'Category']);

$sql = "SELECT faqs.*, kb_categories.name FROM faqs LEFT JOIN kb_categories ON faqs.category_id = kb_categories.id";
$stmt = $pdo->query($sql);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  fputcsv($output, [$row['question'], $row['answer'], $row['name']]);
}
fclose($output);
exit;
?>
