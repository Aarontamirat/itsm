<?php
session_start();
require_once '../config/db.php';

$q = $_GET['q'] ?? '';
$category_id = $_GET['category_id'] ?? '';

$sql = "SELECT kb_articles.*, kb_categories.name AS kb_categories 
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

$sql .= " ORDER BY kb_articles.created_at DESC LIMIT 100"; // limit for performance

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($articles);
