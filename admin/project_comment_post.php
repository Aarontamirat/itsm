<?php
require '../config/db.php'; session_start();
if(!isset($_SESSION['user_id'],$_POST['comment'],$_POST['project_id'])) { die; }
$stmt = $pdo->prepare("INSERT INTO project_comments (project_id,user_id,comment) VALUES (?,?,?)");
$stmt->execute([intval($_POST['project_id']), $_SESSION['user_id'], trim($_POST['comment'])]);
header("Location: project_detail.php?id=".intval($_POST['project_id']));
