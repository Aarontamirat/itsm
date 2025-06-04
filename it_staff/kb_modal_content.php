<?php
session_start();
require_once '../config/db.php';

$user_role = $_SESSION['role'] ?? '';
if (!in_array($user_role, ['admin', 'staff'])) {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? null;

function esc($str) {
  return htmlspecialchars($str, ENT_QUOTES);
}

if ($type === 'add_article' || $type === 'edit_article') {
    $title = $content = '';
    $category_id = '';
    if ($type === 'edit_article' && $id) {
        $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM kb_articles WHERE id = ?");
        $stmt->execute([$id]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$article) {
            echo "<p class='text-red-600'>Article not found.</p>";
            exit;
        }
        $title = $article['title'];
        $content = $article['content'];
        $category_id = $article['category_id'];
    }
    // Fetch categories for select
    $stmt = $GLOBALS['pdo']->prepare("SELECT id, name FROM kb_categories ORDER BY name");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<h2 class="text-2xl font-bold mb-6 font-mono text-cyan-700">' . ($type === 'add_article' ? 'Add New Article' : 'Edit Article') . '</h2>';
    echo '<form id="articleForm" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-xl p-6">';
    if ($type === 'edit_article') {
      echo '<input type="hidden" name="id" value="'.esc($id).'">';
    }
    echo '
      <input type="hidden" name="action" value="'.($type === 'add_article' ? 'add_article' : 'edit_article').'">
      <label class="block mb-5 font-mono font-semibold text-cyan-700">Title
        <input type="text" name="title" value="'.esc($title).'" required class="w-full border border-cyan-300 focus:border-cyan-500 rounded-xl p-3 mt-2 bg-cyan-50 focus:bg-white transition font-mono text-cyan-900 shadow-inner">
      </label>
      <label class="block mb-5 font-mono font-semibold text-cyan-700">Content
        <textarea name="content" rows="8" required class="w-full border border-cyan-300 focus:border-cyan-500 rounded-xl p-3 mt-2 bg-cyan-50 focus:bg-white transition font-mono text-cyan-900 shadow-inner">'.esc($content).'</textarea>
      </label>
      <label class="block mb-7 font-mono font-semibold text-cyan-700">Category
        <select name="category_id" required class="w-full border border-cyan-300 focus:border-cyan-500 rounded-xl p-3 mt-2 bg-cyan-50 focus:bg-white transition font-mono text-cyan-900 shadow-inner">
          <option value="">Select category</option>';
          foreach ($categories as $cat) {
            $sel = ($cat['id'] == $category_id) ? 'selected' : '';
            echo '<option value="'.esc($cat['id']).'" '.$sel.'>'.esc($cat['name']).'</option>';
          }
    echo '</select>
      </label>
      <div class="flex justify-end gap-3 mt-8">
      <button type="button" class="modalCloseBtn bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
        Cancel
      </button>
      <button type="submit" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
        Save
      </button>
      </div>
    </form>
    </div>';
    exit;
}

if ($type === 'add_category' || $type === 'edit_category') {
    $name = '';
    if ($type === 'edit_category' && $id) {
        $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM kb_categories WHERE id = ?");
        $stmt->execute([$id]);
        $cat = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$cat) {
            echo "<p class='text-red-600'>Category not found.</p>";
            exit;
        }
        $name = $cat['cat_name'];
    }
    echo '<h2 class="text-2xl font-bold mb-6 font-mono text-cyan-700">' . ($type === 'add_category' ? 'Add New Category' : 'Edit Category') . '</h2>';
    echo '<form id="categoryForm" class="bg-white rounded-2xl shadow-xl p-6">';
    if ($type === 'edit_category') {
      echo '<input type="hidden" name="id" value="'.esc($id).'">';
    }
    echo '
      <input type="hidden" name="action" value="'.($type === 'add_category' ? 'add_category' : 'edit_category').'">
      <label class="block mb-7 font-mono font-semibold text-cyan-700">Category Name
      <input type="text" name="cat_name" value="'.esc($name).'" required class="w-full border border-cyan-300 focus:border-cyan-500 rounded-xl p-3 mt-2 bg-cyan-50 focus:bg-white transition font-mono text-cyan-900 shadow-inner">
      </label>
      <div class="flex justify-end gap-3 mt-8">
      <button type="button" class="modalCloseBtn bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
        Cancel
      </button>
      <button type="submit" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
        Save
      </button>
      </div>
    </form>';
    exit;
}

if ($type === 'delete_article' && $id) {
    echo '<h2 class="text-xl font-semibold mb-4">Delete Article</h2>';
    echo '<p>Are you sure you want to delete this article?</p>';
    echo '<form id="deleteForm">';
    echo '<input type="hidden" name="action" value="delete_article">';
    echo '<input type="hidden" name="id" value="'.esc($id).'">';
    echo '<div class="flex justify-end gap-3 mt-4">';
    echo '<button type="button" class="modalCloseBtn bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>';
    echo '<button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Delete</button>';
    echo '</div></form>';
    exit;
}

if ($type === 'delete_category' && $id) {
    echo '<h2 class="text-xl font-semibold mb-4">Delete Category</h2>';
    echo '<p>Are you sure you want to delete this category?</p>';
    echo '<form id="deleteForm">';
    echo '<input type="hidden" name="action" value="delete_category">';
    echo '<input type="hidden" name="id" value="'.esc($id).'">';
    echo '<div class="flex justify-end gap-3 mt-4">';
    echo '<button type="button" class="modalCloseBtn bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>';
    echo '<button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Delete</button>';
    echo '</div></form>';
    exit;
}

echo "<p>Invalid request</p>";
