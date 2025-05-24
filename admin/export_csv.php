<?php
require '../config/db.php';

// Fetch incidents for CSV export
$stmt = $pdo->query(
    "SELECT 
        i.*,
        u.name AS assigned_to_name
    FROM 
        incidents i
    LEFT JOIN
        users u ON i.assigned_to = u.id
    WHERE 
        i.status IN ('open', 'assigned', 'in_progress', 'resolved')
    ORDER BY 
        created_at DESC"
    );
$incidents = $stmt->fetchAll();

// Set headers for CSV file download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="incidents_report.csv"');

// Open the output stream
$output = fopen('php://output', 'w');

// Add column headers to CSV
fputcsv($output, ['ID', 'Title', 'Description', 'Priority', 'Status', 'Assigned To', 'Submitted By', 'Created At']);

// Add each incident as a row in CSV
foreach ($incidents as $incident) {
    fputcsv($output, $incident);
}

fclose($output);
exit;
