<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    die("Unauthorized");
}

$id = $_POST['id'];
$stmt = $pdo->prepare("DELETE FROM faqs WHERE id = ?");
$stmt->execute([$id]);

header("Location: faq_list.php");
exit;
