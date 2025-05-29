<?php
// DB connection
require_once '../config/db.php';
session_start();

// Fetch categories
$stmt = $pdo->query("SELECT * FROM faq_categories ORDER BY category_name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Knowledge Base</title>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2"></script>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <div class="container mx-auto p-8">
    <h1 class="text-3xl font-bold mb-6 text-center">Knowledge Base</h1>

    <!-- Search + Filter -->
    <div class="flex flex-wrap gap-4 mb-6 justify-center">
      <input type="text" id="searchInput" placeholder="Search FAQs..." class="border p-2 rounded w-64">
      
      <select id="categoryFilter" class="border p-2 rounded">
        <option value="">All Categories</option>
        <?php foreach($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
        <?php endforeach; ?>
      </select>

      <button id="exportPDF" class="bg-red-600 text-white px-4 py-2 rounded">Export PDF</button>
      <button id="exportCSV" class="bg-green-600 text-white px-4 py-2 rounded">Export CSV</button>
    </div>

    <!-- FAQs Display -->
    <div id="faqList" class="space-y-4">
      <!-- FAQs will load here via AJAX -->
    </div>
  </div>

  <script>
    function loadFAQs() {
      const search = $('#searchInput').val();
      const category = $('#categoryFilter').val();

      $.ajax({
        url: 'fetch_faqs.php',
        type: 'GET',
        data: { search, category },
        success: function(data) {
          $('#faqList').html(data);
        }
      });
    }

    $('#searchInput, #categoryFilter').on('input change', loadFAQs);
    $(document).ready(loadFAQs);

    $('#exportPDF').click(() => window.location.href = 'export_faqs_pdf.php');
    $('#exportCSV').click(() => window.location.href = 'export_faqs_csv.php');
  </script>

</body>
</html>
