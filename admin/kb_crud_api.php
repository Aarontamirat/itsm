<?php
session_start();
require_once '../config/db.php';

$user_role = $_SESSION['role'] ?? '';
if (!in_array($user_role, ['admin', 'staff'])) {
    http_response_code(403);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';

function respond($msg, $success = true) {
    echo json_encode(['message' => $msg, 'success' => $success]);
    exit;
}

if ($action === 'add_article') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category_id = $_POST['category_id'] ?? null;

    if ($title === '' || $content === '' || !$category_id || !is_numeric($category_id)) {
        respond('Invalid input', false);
    }

    $stmt = $pdo->prepare("INSERT INTO kb_articles (title, content, category_id, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$title, $content, $category_id]);

    respond('Article added successfully');

} elseif ($action === 'edit_article') {
    $id = $_POST['id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category_id = $_POST['category_id'] ?? null;

    if (!$id || !is_numeric($id) || $title === '' || $content === '' || !$category_id || !is_numeric($category_id)) {
        respond('Invalid input', false);
    }

    $stmt = $pdo->prepare("UPDATE kb_articles SET title = ?, content = ?, category_id = ? WHERE id = ?");
    $stmt->execute([$title, $content, $category_id, $id]);

    respond('Article updated successfully');

} elseif ($action === 'delete_article') {
    $id = $_POST['id'] ?? null;
    if (!$id || !is_numeric($id)) {
        respond('Invalid article ID', false);
    }
    $stmt = $pdo->prepare("DELETE FROM kb_articles WHERE id = ?");
    $stmt->execute([$id]);

    respond('Article deleted successfully');

} elseif ($action === 'add_category') {
    $kb_categories = trim($_POST['kb_categories'] ?? '');
    if ($kb_categories === '') {
        respond('Category name required', false);
    }
    $stmt = $pdo->prepare("INSERT INTO kb_categories (name, created_at) VALUES (?, NOW())");
    $stmt->execute([$kb_categories]);

    respond('Category added successfully');

} elseif ($action === 'edit_category') {
    $id = $_POST['id'] ?? null;
    $kb_categories = trim($_POST['kb_categories'] ?? '');
    if (!$id || !is_numeric($id) || $kb_categories === '') {
        respond('Invalid input', false);
    }
    $stmt = $pdo->prepare("UPDATE kb_categories SET name = ? WHERE id = ?");
    $stmt->execute([$kb_categories, $id]);

    respond('Category updated successfully');

} elseif ($action === 'delete_category') {
    $id = $_POST['id'] ?? null;
    if (!$id || !is_numeric($id)) {
        respond('Invalid category ID', false);
    }
    $stmt = $pdo->prepare("DELETE FROM kb_categories WHERE id = ?");
    $stmt->execute([$id]);

    respond('Category deleted successfully');

} else {
    respond('Invalid action', false);
}
