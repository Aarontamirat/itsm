<?php
require_once '../config/db.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "SELECT f.*, c.name, u.name AS created_by_name FROM faqs f
        LEFT JOIN faq_categories c ON f.category_id = c.id
        LEFT JOIN users u ON f.created_by = u.id
        WHERE (f.title LIKE ? OR f.solution LIKE ?)";
$params = ["%$search%", "%$search%"];

if ($category) {
  $sql .= " AND f.category_id = ?";
  $params[] = $category;
}

$sql .= " ORDER BY f.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($faqs) {
  foreach ($faqs as $faq) {
    echo '<div class="bg-white rounded shadow p-4">';
    echo '<button class="faq-toggle w-full text-left font-semibold text-lg">' . htmlspecialchars($faq['title']) . '</button>';
    echo '<div class="faq-content hidden mt-2 text-gray-700">';
    echo '<p>' . nl2br(htmlspecialchars($faq['solution'])) . '</p>';
    echo '<p class="mt-2 text-sm text-gray-500">Category: ' . htmlspecialchars($faq['name']) . ' | By: ' . htmlspecialchars($faq['created_by_name']) . '</p>';
    echo '</div>';
    echo '</div>';
  }
} else {
  echo '<p class="text-gray-500 text-center">No FAQs found. Try searching or selecting a category.</p>';
}
?>
<script>
  $('.faq-toggle').click(function() {
    $(this).next('.faq-content').slideToggle(200);
  });
</script>
