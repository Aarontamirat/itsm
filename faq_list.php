<?php
session_start();
require 'config/db.php';

// Check if user is logged in and has the right role
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// Handle FAQ submission
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

// Fetch all categories for dropdown
$catStmt = $pdo->query("SELECT DISTINCT category FROM faqs ORDER BY category ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

// Count total matching FAQs
$countQuery = "SELECT COUNT(*) FROM faqs WHERE (title LIKE ? OR category LIKE ? OR solution LIKE ?)";
$params = ["%$search%", "%$search%", "%$search%"];
if ($category !== '') {
    $countQuery .= " AND category = ?";
    $params[] = $category;
}
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $limit);

// Fetch paginated FAQs
$query = "SELECT f.*, u.name AS author FROM faqs f JOIN users u ON f.created_by = u.id 
          WHERE (f.title LIKE ? OR f.category LIKE ? OR f.solution LIKE ?)";
if ($category !== '') {
    $query .= " AND f.category = ?";
}
$query .= " ORDER BY f.created_at DESC LIMIT $limit OFFSET $offset";
$dataStmt = $pdo->prepare($query);
$dataStmt->execute($params);
$faqs = $dataStmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>FAQ Knowledge Base</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">


<!-- header and sidebar -->
      <?php include 'includes/sidebar.php'; ?>
  <div class="flex-1 ml-20">
    <?php include 'includes/header.php'; ?>


    <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">📚 FAQ Knowledge Base</h2>

       <?php // Check if user is admin or IT staff
if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'staff'): ?>
    <button onclick="openFaqModal()" class="bg-blue-600 text-white px-4 py-2 my-2 rounded hover:bg-blue-700">
      + Submit New FAQ
    </button>
  <?php endif; 
?>
<!-- FAQ Modal to submit new FAQs by Admin and IT_Staff -->
        <div id="faqModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
  <div class="bg-white w-full max-w-lg p-6 rounded shadow-lg relative">
    <h2 class="text-xl font-bold mb-4">Submit New FAQ</h2>
    <form id="faqForm" method="POST" action="submit_faq.php">
      <label class="block mb-2">Title:</label>
      <input type="text" name="title" class="w-full p-2 border rounded mb-4" required>

      <label class="block mb-2">Category:</label>
      <input type="text" name="category" class="w-full p-2 border rounded mb-4" required>

      <label class="block mb-2">Solution:</label>
      <textarea name="solution" rows="5" class="w-full p-2 border rounded mb-4" required></textarea>

      <label class="block mb-2">Linked Incident (Optional):</label>
      <input type="number" name="linked_incident" class="w-full p-2 border rounded mb-4">

      <div class="flex justify-end gap-2">
        <button type="button" onclick="closeFaqModal()" class="px-4 py-2 bg-gray-400 rounded">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Submit</button>
      </div>
    </form>
    <button onclick="closeFaqModal()" class="absolute top-2 right-2 text-gray-500 text-xl">&times;</button>
  </div>
  
</div>

<!-- FAQ LIST SEARCH -->
        <form method="GET" class="mb-6 flex flex-col sm:flex-row gap-2">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="border p-2 rounded w-full sm:w-1/2" placeholder="Search FAQs...">

            <select name="category" class="border p-2 rounded w-full sm:w-1/3">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button class="bg-blue-600 text-white px-4 rounded">Search</button>
        </form>

        <div class="flex justify-end gap-3 mb-4">
            <a href="export_faq.php?format=csv&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>" class="bg-green-600 text-white px-3 py-1 rounded text-sm">Export CSV</a>
            <a href="export_faq.php?format=pdf&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>" class="bg-red-600 text-white px-3 py-1 rounded text-sm">Export PDF</a>
        </div>

        <!-- FAQ LIST -->
        <?php if (count($faqs) === 0): ?>
            <p>No FAQs found matching your filters.</p>
        <?php elseif (isset($_GET['category']) || isset($_GET['search'])): ?>
            <div class="space-y-4">
                <?php foreach ($faqs as $faq): ?>
                    <div class="border p-4 rounded shadow-sm bg-gray-50">
                        <h3 class="text-xl font-semibold"><?= htmlspecialchars($faq['title']) ?></h3>
                        <p class="text-sm text-gray-600 mb-2">Category: <?= htmlspecialchars($faq['category']) ?> | By: <?= htmlspecialchars($faq['author']) ?></p>
                        <p class="text-gray-800 whitespace-pre-line"><?= nl2br(htmlspecialchars($faq['solution'])) ?></p>
                        <?php if ($faq['linked_incident']): ?>
                            <p class="text-sm text-blue-600 mt-2">🔗 Related Incident ID: <?= $faq['linked_incident'] ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- show edit and delete button for admins and it_staff -->
                    <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'staff'): ?>
                        
                    <button onclick="openEditFaqModal(<?= $faq['id'] ?>, <?= htmlspecialchars(json_encode($faq), ENT_QUOTES, 'UTF-8') ?>)"
                    class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                    <form action="delete_faq.php" method="POST" class="inline">
                    <input type="hidden" name="id" value="<?= $faq['id'] ?>">
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this FAQ?')"
                        class="bg-red-600 text-white px-2 py-1 rounded">Delete</button>
                    </form>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-center items-center space-x-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&page=<?= $i ?>"
                       class="px-3 py-1 rounded <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php else: ?>
        <p>Click Search to See.</p>
        <?php endif; ?>
    </div>

    <!-- JavaScript for FAQ submmiting modal -->
    <script>
  function openFaqModal() {
    document.getElementById('faqModal').classList.remove('hidden');
  }

  function closeFaqModal() {
    document.getElementById('faqModal').classList.add('hidden');
  }
</script>

<!-- JavaScript for editing FAQs modal -->
<script>
function openEditFaqModal(id, faq) {
  document.getElementById('edit_id').value = id;
  document.getElementById('edit_title').value = faq.title;
  document.getElementById('edit_category').value = faq.category;
  document.getElementById('edit_solution').value = faq.solution;
  document.getElementById('edit_incident').value = faq.linked_incident || '';
  document.getElementById('editFaqModal').classList.remove('hidden');
}

function closeEditFaqModal() {
  document.getElementById('editFaqModal').classList.add('hidden');
}
</script>


</body>
</html>