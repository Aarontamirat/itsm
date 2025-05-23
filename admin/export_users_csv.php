<?php
require '../config/db.php';

$branchFilter = $_GET['branch_id'] ?? '';

if ($branchFilter) {
    $stmt = $pdo->prepare("SELECT u.*, b.name AS branch_name FROM users u
        LEFT JOIN branches b ON u.branch_id = b.id
        WHERE u.branch_id = ?");
    $stmt->execute([$branchFilter]);
} else {
    $stmt = $pdo->query("SELECT u.*, b.name AS branch_name FROM users u
        LEFT JOIN branches b ON u.branch_id = b.id");
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=users.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Name', 'Email', 'Role', 'Branch']);

foreach ($stmt as $row) {
    fputcsv($output, [
        $row['id'],
        $row['name'],
        $row['email'],
        $row['role'],
        $row['branch_name'] ?? 'N/A'
    ]);
}

fclose($output);
exit;
