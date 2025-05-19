<?php
session_start();
require 'config/db.php'; // the PDO config

header('Content-Type: application/json');


if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

try {
    // Fetch unseen notifications first
    $stmt = $pdo->prepare("
        SELECT id, message FROM notifications
        WHERE user_id = :uid AND seen = 0
        ORDER BY created_at DESC
        LIMIT 10
    ");
    $stmt->execute([
        ':uid' => $user_id,
    ]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // mark them as seen
    $update = $pdo->prepare("
        UPDATE notifications
        SET seen = 1
        WHERE user_id = :uid AND seen = 0
    ");
    $update->execute([
        ':uid' => $user_id,
    ]);

    echo json_encode(["notifications" => $notifications]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "DB Error"]);
}
