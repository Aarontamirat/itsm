<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    die("Unauthorized");
}

$title = trim($_POST['title']);
$category = trim($_POST['category']);
$solution = trim($_POST['solution']);
$incident = !empty($_POST['linked_incident']) ? intval($_POST['linked_incident']) : null;

$stmt = $pdo->prepare("INSERT INTO faqs (title, category, solution, created_by, linked_incident, created_at) 
                       VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->execute([$title, $category, $solution, $_SESSION['user_id'], $incident]);

header("Location: faq_list.php");
exit;
