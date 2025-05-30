<?php
session_start();
require_once '../config/db.php';

// Check user role for permission
$user_role = $_SESSION['role'] ?? '';
$is_admin_or_staff = in_array($user_role, ['admin', 'staff']);

// Fetch categories for filter dropdown
$categories = [];
$stmt = $pdo->prepare("SELECT id, name FROM kb_categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Knowledge Base</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body class="bg-gray-100">

<?php include '../includes/sidebar.php'; ?>
<?php include '../header.php'; ?>

  <div class="max-w-6xl mt-4 ms-auto p-4">

    <header class="flex items-center justify-between mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Knowledge Base</h1>

      <?php if($is_admin_or_staff): ?>
        <button id="addArticleBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">+ Add Article</button>
        <button id="addCategoryBtn" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded shadow ml-3">+ Add Category</button>
      <?php endif; ?>
    </header>

    <!-- Search & Filter -->
    <div class="flex flex-wrap gap-3 mb-5">
      <input
        type="text"
        id="searchInput"
        placeholder="Search articles..."
        class="flex-grow p-2 border border-gray-300 rounded"
      />
      <select id="categoryFilter" class="p-2 border border-gray-300 rounded">
        <option value="">All Categories</option>
        <?php foreach($categories as $cat): ?>
          <option value="<?= htmlspecialchars($cat['id']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <button id="searchBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow">Search</button>

      <button id="exportCsvBtn" class="ml-auto bg-gray-700 hover:bg-gray-900 text-white px-4 py-2 rounded shadow">Export CSV</button>
      <button id="exportPdfBtn" class="bg-gray-700 hover:bg-gray-900 text-white px-4 py-2 rounded shadow ml-2">Export PDF</button>
    </div>

    <!-- Articles Accordion Container -->
    <div id="articlesContainer" class="space-y-4">
      <!-- No results initially -->
      <p id="noResultsMsg" class="text-center text-gray-500">Search or filter to see articles.</p>
    </div>
  </div>

  <!-- Modals -->
  <div id="modalBackdrop" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div id="modalContent" class="bg-white rounded shadow-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto p-6 relative">
      <!-- Dynamic modal content will go here -->
    </div>
  </div>

<script>
  // Elements
  const searchInput = document.getElementById('searchInput');
  const categoryFilter = document.getElementById('categoryFilter');
  const searchBtn = document.getElementById('searchBtn');
  const articlesContainer = document.getElementById('articlesContainer');
  const noResultsMsg = document.getElementById('noResultsMsg');
  const modalBackdrop = document.getElementById('modalBackdrop');
  const modalContent = document.getElementById('modalContent');
  const exportCsvBtn = document.getElementById('exportCsvBtn');
  const exportPdfBtn = document.getElementById('exportPdfBtn');

  // Optional buttons for admin/staff
  const addArticleBtn = document.getElementById('addArticleBtn');
  const addCategoryBtn = document.getElementById('addCategoryBtn');

  // Utility: Close modal function
  function closeModal() {
    modalBackdrop.classList.add('hidden');
    modalContent.innerHTML = '';
  }

  modalBackdrop.addEventListener('click', e => {
    if (e.target === modalBackdrop) closeModal();
  });

  // Accordion toggler
  function toggleAccordion(event) {
    const header = event.currentTarget;
    const content = header.nextElementSibling;
    const isOpen = content.classList.contains('max-h-screen');
    if (isOpen) {
      content.style.maxHeight = null;
      content.classList.remove('max-h-screen');
    } else {
      content.style.maxHeight = content.scrollHeight + "px";
      content.classList.add('max-h-screen');
    }
  }

  // Fetch articles (AJAX)
  async function fetchArticles() {
    const q = searchInput.value.trim();
    const catId = categoryFilter.value;

    // Clear current content
    articlesContainer.innerHTML = '';
    noResultsMsg.style.display = 'block';
    noResultsMsg.textContent = 'Loading...';

    try {
      const response = await axios.get('kb_api.php', {
        params: { q, category_id: catId }
      });
      const articles = response.data;

      if (!articles.length) {
        noResultsMsg.textContent = 'No articles found.';
        return;
      }

      noResultsMsg.style.display = 'none';

      // Build accordion articles
      articles.forEach(article => {
        const card = document.createElement('div');
        card.className = "bg-white rounded shadow p-4";

        const header = document.createElement('button');
        header.className = "w-full text-left font-semibold text-lg text-indigo-700 hover:text-indigo-900 focus:outline-none";
        header.textContent = article.title;
        header.addEventListener('click', toggleAccordion);

        const content = document.createElement('div');
        content.className = "overflow-hidden max-h-0 transition-max-height duration-300 ease-in-out mt-2 text-gray-700";
        content.innerHTML = `
          <div class="prose max-w-none mb-3">${article.content}</div>
          <div class="text-sm text-gray-500">Category: ${article.name}</div>
          <div class="text-xs text-gray-400 mt-1">Created at: ${new Date(article.created_at).toLocaleString()}</div>
          <div class="mt-3">
            <button data-article-id="${article.id}" class="feedbackBtn mr-3 px-3 py-1 bg-green-100 text-green-800 rounded hover:bg-green-200">Helpful</button>
            <button data-article-id="${article.id}" class="feedbackBtn px-3 py-1 bg-red-100 text-red-800 rounded hover:bg-red-200">Not Helpful</button>
          </div>
        `;

        card.appendChild(header);
        card.appendChild(content);
        articlesContainer.appendChild(card);
      });

      // Add feedback event listeners
      document.querySelectorAll('.feedbackBtn').forEach(btn => {
        btn.addEventListener('click', async e => {
          const articleId = e.target.dataset.articleId;
          const feedbackType = e.target.textContent.toLowerCase().includes('helpful') ? 'helpful' : 'not_helpful';
          try {
            await axios.post('kb_feedback_api.php', { article_id: articleId, feedback_type: feedbackType });
            alert('Thank you for your feedback!');
          } catch {
            alert('Error submitting feedback.');
          }
        });
      });

    } catch (error) {
      noResultsMsg.textContent = 'Error loading articles.';
      console.error(error);
    }
  }

  searchBtn.addEventListener('click', fetchArticles);
  searchInput.addEventListener('keydown', e => {
    if (e.key === 'Enter') fetchArticles();
  });

  categoryFilter.addEventListener('change', fetchArticles);

  // Export handlers
  exportCsvBtn.addEventListener('click', () => {
    const q = encodeURIComponent(searchInput.value.trim());
    const catId = encodeURIComponent(categoryFilter.value);
    window.open(`kb_export_csv.php?q=${q}&category_id=${catId}`, '_blank');
  });

  exportPdfBtn.addEventListener('click', () => {
    const q = encodeURIComponent(searchInput.value.trim());
    const catId = encodeURIComponent(categoryFilter.value);
    window.open(`kb_export_pdf.php?q=${q}&category_id=${catId}`, '_blank');
  });

  // MODAL FUNCTIONS (Add/Edit/Delete for categories and articles)
  <?php if($is_admin_or_staff): ?>
  addArticleBtn?.addEventListener('click', () => {
    openModal('add_article');
  });
  addCategoryBtn?.addEventListener('click', () => {
    openModal('add_category');
  });

  async function openModal(type, id = null) {
    modalBackdrop.classList.remove('hidden');
    modalContent.innerHTML = 'Loading...';

    try {
      const response = await axios.get('kb_modal_content.php', { params: { type, id } });
      modalContent.innerHTML = response.data;

      // Bind form submit after modal loads
      const form = modalContent.querySelector('form');
      form?.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData(form);

        try {
          const submitResponse = await axios.post('kb_crud_api.php', formData);
          alert(submitResponse.data.message);
          closeModal();
          fetchArticles();
        } catch (error) {
          alert(error.response?.data?.message || 'Error processing request');
        }
      });

      // Bind modal close button
      modalContent.querySelector('.modalCloseBtn')?.addEventListener('click', closeModal);

    } catch (error) {
      modalContent.innerHTML = '<p class="text-red-600">Failed to load modal content.</p>';
    }
  }

  <?php endif; ?>
</script>
</body>
</html>
