<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    die("Unauthorized");
}

$id = $_POST['id'];
$title = trim($_POST['title']);
$category = trim($_POST['category']);
$solution = trim($_POST['solution']);
$incident = !empty($_POST['linked_incident']) ? intval($_POST['linked_incident']) : null;

$stmt = $pdo->prepare("UPDATE faqs SET title = ?, category = ?, solution = ?, linked_incident = ? WHERE id = ?");
$stmt->execute([$title, $category, $solution, $incident, $id]);

header("Location: faq_list.php");
exit;
