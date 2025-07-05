<?php
require '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $deadline_date = !empty($_POST['deadline_date']) ? $_POST['deadline_date'] : NULL;

    if ($title === '') {
        $_SESSION['error'] = "Title cannot be empty.";
        header('Location: projects.php');
        exit;
    }
    if ($deadline_date === '' || $deadline_date === null) {
        $_SESSION['error'] = "Deadline cannot be empty.";
        header('Location: projects.php');
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO projects (title, description, deadline_date, created_by) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $description, $deadline_date, $_SESSION['user_id']]);

    $_SESSION['success'] = "Project created successfully!";
    header('Location: projects.php');
    exit;
}
