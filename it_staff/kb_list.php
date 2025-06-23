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

  <div class="max-w-7xl ms-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 pt-20 fade-in tech-border glow mt-8">
    <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Knowledge Base</h2>
    <p class="text-center text-cyan-500 mb-1 font-mono">Browse and manage IT Support knowledge articles</p>

    <!-- Success/Error Messages (optional, for future use) -->
    <?php if (isset($_SESSION['success'])): ?>
      <div id="success-message" class="mb-4 text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
        <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
      </div>
      <script>
        setTimeout(function() {
          var el = document.getElementById('success-message');
          if (el) el.style.opacity = '1';
        }, 10);
        setTimeout(function() {
          var el = document.getElementById('success-message');
          if (el) el.style.opacity = '0';
        }, 3010);
      </script>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
      <div id="error-message" class="mb-4 text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
      </div>
      <script>
        setTimeout(function() {
          var el = document.getElementById('error-message');
          if (el) el.style.opacity = '1';
        }, 10);
        setTimeout(function() {
          var el = document.getElementById('error-message');
          if (el) el.style.opacity = '0';
        }, 3010);
      </script>
    <?php endif; ?>

    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
      <div class="flex flex-row items-center gap-3">
        <?php if($is_admin_or_staff): ?>
          <button id="addArticleBtn" class="bg-gradient-to-r from-cyan-200 via-cyan-100 to-green-100 hover:from-green-200 hover:to-cyan-200 text-cyan-800 font-bold rounded-lg shadow px-6 py-2 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
        + Add Article
          </button>
          <button id="addCategoryBtn" class="bg-gradient-to-r from-amber-200 via-amber-100 to-yellow-100 hover:from-yellow-200 hover:to-amber-200 text-amber-800 font-bold rounded-lg shadow px-6 py-2 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
        + Add Category
          </button>
        <?php endif; ?>
      </div>
      <div class="flex flex-col md:flex-row items-center gap-4">
        <div class="flex flex-row gap-2 items-center">
          <button id="exportCsvBtn" class="inline-block px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-mono font-semibold shadow transition">
            Export CSV
          </button>
          <button id="exportPdfBtn" class="inline-block px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-mono font-semibold shadow transition">
            Export PDF
          </button>
        </div>
        <form onsubmit="fetchArticles(); return false;" class="flex items-center gap-2">
          <label for="categoryFilter" class="font-mono text-cyan-700 font-semibold">Category:</label>
          <select id="categoryFilter" class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono">
            <option value="">All Categories</option>
            <?php foreach($categories as $cat): ?>
              <option value="<?= htmlspecialchars($cat['id']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <input
            type="text"
            id="searchInput"
            placeholder="Search articles..."
            class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono"
          />
          <button id="searchBtn" type="button" class="px-4 py-2 bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest">
            Search
          </button>
        </form>
      </div>
    </div>

    <!-- Articles Accordion Container -->
    <div id="articlesContainer" class="space-y-4">
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
      card.className = "bg-cyan-50 bg-opacity-80 rounded-xl shadow p-4 border border-cyan-100";

      const header = document.createElement('button');
      header.className = "w-full text-left font-semibold text-lg text-cyan-700 hover:text-cyan-900 focus:outline-none font-mono";
      header.textContent = article.title;
      header.addEventListener('click', toggleAccordion);

      const content = document.createElement('div');
      content.className = "overflow-hidden max-h-0 transition-max-height duration-300 ease-in-out mt-2 text-cyan-900 font-mono";
      content.innerHTML = `
        <div class="prose max-w-none mb-3">${article.content}</div>
        <div class="text-sm text-cyan-500">Category: ${article.name}</div>
        <div class="text-xs text-cyan-400 mt-1">Created at: ${new Date(article.created_at).toLocaleString()}</div>
        <div class="mt-3 flex gap-2">
          <button data-article-id="${article.id}" class="feedbackBtn px-3 py-1 bg-green-100 text-green-800 rounded hover:bg-green-200 font-mono">Good</button>
          <button data-article-id="${article.id}" class="feedbackBtn px-3 py-1 bg-red-100 text-red-800 rounded hover:bg-red-200 font-mono">Bad</button>
          ${<?= json_encode($is_admin_or_staff) ?> ? `
            <button data-article-id="${article.id}" class="editArticleBtn px-3 py-1 bg-cyan-100 text-cyan-800 rounded hover:bg-cyan-200 font-mono">Edit</button>
            <button data-article-id="${article.id}" class="deleteArticleBtn px-3 py-1 bg-red-100 text-red-800 rounded hover:bg-red-200 font-mono">Delete</button>
          ` : ''}
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
        const feedbackType = e.target.textContent.toLowerCase().includes('good') ? 'good' : 'bad';
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

    // Pass the current logged in user from PHP to JS
    const createdBy = <?= json_encode($_SESSION['user_id'] ?? '') ?>;

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
        formData.append('created_by', createdBy);

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

    // Edit Article: delegate event to articlesContainer
    articlesContainer.addEventListener('click', function(e) {
      if (e.target.classList.contains('editArticleBtn')) {
      const articleId = e.target.dataset.articleId;
      openModal('edit_article', articleId);
      }
    });

    // Edit Article: delegate event to articlesContainer
    articlesContainer.addEventListener('click', function(e) {
      if (e.target.classList.contains('deleteArticleBtn')) {
      const articleId = e.target.dataset.articleId;
      openModal('delete_article', articleId);
      }
    });
    <?php endif; ?>


  </script>
</body>
</html>
