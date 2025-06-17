<?php
session_start();
require_once '../config/db.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$article_id = $data['article_id'] ?? null;
$feedback_type = $data['feedback_type'] ?? null;

if (!in_array($feedback_type, ['good', 'bad']) || !$article_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Prevent duplicate feedback by same user on same article for same feedback_type (optional)
$stmt = $pdo->prepare("SELECT id FROM kb_feedback WHERE user_id = ? AND article_id = ?");
$stmt->execute([$user_id, $article_id]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'Feedback already submitted']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO kb_feedback (article_id, user_id, feedback_type) VALUES (?, ?, ?)");
$stmt->execute([$article_id, $user_id, $feedback_type]);

echo json_encode(['success' => true]);
