<?php
session_start();
include '../header.php'; // Navigation & CSS
include '../config/db.php';

// Fetch categories for filter
$categories = $pdo->query("SELECT * FROM incident_categories ORDER BY category_name")->fetchAll(PDO::FETCH_ASSOC);

// Fetch FAQs (with optional search and filter)
$where = [];
$params = [];

if (!empty($_GET['search'])) {
  $where[] = "title LIKE ?";
  $params[] = "%" . $_GET['search'] . "%";
}

if (!empty($_GET['category_id'])) {
  $where[] = "category_id = ?";
  $params[] = $_GET['category_id'];
}

$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

$stmt = $pdo->prepare("SELECT faqs.*, incident_categories.category_name FROM faqs LEFT JOIN incident_categories ON faqs.category_id = incident_categories.id $whereSql ORDER BY faqs.created_at DESC");
$stmt->execute($params);
$faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>FAQ Knowledge Base</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-5xl mx-auto p-6">
  <h1 class="text-3xl font-bold text-center text-blue-600 mb-6">Frequently Asked Questions</h1>

  <!-- Search and Filter -->
  <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
    <input type="text" name="search" placeholder="Search by topic..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="p-2 border border-gray-300 rounded w-60">
    <select name="category_id" class="p-2 border border-gray-300 rounded">
      <option value="">All Categories</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>" <?= (($_GET['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat['category_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Filter</button>
  </form>

  <!-- FAQ List -->
  <div class="space-y-4">
    <?php if ($faqs): ?>
      <?php foreach ($faqs as $faq): ?>
        <div class="border border-gray-300 rounded-lg shadow-md">
          <button class="flex justify-between items-center w-full p-4 text-left text-lg font-semibold bg-gray-100 hover:bg-blue-100 transition">
            <span><?= htmlspecialchars($faq['title']) ?></span>
            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          <div class="hidden p-4 bg-white border-t">
            <p><?= nl2br(htmlspecialchars($faq['solution'])) ?></p>
            <p class="mt-2 text-sm text-gray-500">Category: <?= htmlspecialchars($faq['category_name'] ?? 'N/A') ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center text-gray-500">No FAQs found.</p>
    <?php endif; ?>
  </div>
</div>

<script>
// Accordion toggle
document.querySelectorAll("button").forEach(btn => {
  btn.addEventListener("click", () => {
    const content = btn.nextElementSibling;
    const icon = btn.querySelector("svg");
    content.classList.toggle("hidden");
    icon.classList.toggle("rotate-180");
  });
});
</script>
