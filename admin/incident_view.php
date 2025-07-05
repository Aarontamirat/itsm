<?php
// /c:/xampp/htdocs/itsm/it_staff/incident_view.php

require_once '../config/db.php';

// Get incident ID from query string
$incident_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($incident_id <= 0) {
    die('<div style="color:red;text-align:center;margin-top:50px;">Invalid Incident ID.</div>');
}

// Fetch incident details
$stmt = $pdo->prepare(
    "SELECT 
        i.*, f.filepath, u2.name AS assigned_to_name, u.name AS submitted_by_name, c.name AS name
    FROM 
        incidents i
    LEFT JOIN 
        files f ON i.id = f.incident_id
    LEFT JOIN 
        users u ON i.submitted_by = u.id
    LEFT JOIN 
        users u2 ON i.assigned_to = u2.id
    LEFT JOIN 
        kb_categories c ON i.category_id = c.id
    WHERE 
        i.id = ?
    "
);
$stmt->bindValue(1, $incident_id, PDO::PARAM_INT);
$stmt->execute();
$incident = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$incident) {
    die('<div style="color:red;text-align:center;margin-top:50px;">Incident not found.</div>');
}

// Helper function for safe output
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Status badge color
function statusBadge($status) {
    $colors = [
        'Open' => 'badge-danger',
        'In Progress' => 'badge-warning',
        'Resolved' => 'badge-success',
        'Closed' => 'badge-secondary'
    ];
    return $colors[$status] ?? 'badge-info';
}

// Priority badge color
function priorityBadge($priority) {
    $colors = [
        'Low' => 'badge-success',
        'Medium' => 'badge-warning',
        'High' => 'badge-danger',
        'Critical' => 'badge-dark'
    ];
    return $colors[$priority] ?? 'badge-info';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Incident Viewer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="max-w-3xl mx-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 fade-in tech-border glow mt-8 font-mono">
        <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight">Incident Details</h2>
        <p class="text-center text-cyan-500 mb-6">View and manage incident information</p>

        <div class="flex flex-col md:flex-row justify-center items-center gap-4 mb-8">
            <a href="incidents.php" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 transform hover:scale-105 transition duration-300 tracking-widest">
                &larr; Back to List
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <div class="text-cyan-700 font-semibold mb-1">Title:</div>
                <div class="text-xl font-bold text-cyan-900 mb-2"><?= h($incident['title']) ?></div>
                <div class="mb-3">
                    <span class="text-cyan-700 font-semibold">Status:</span>
                    <span class="inline-block px-3 py-1 rounded-lg font-bold text-white <?= statusBadge($incident['status']) == 'badge-danger' ? 'bg-red-500' : (statusBadge($incident['status']) == 'badge-warning' ? 'bg-yellow-400 text-cyan-900' : (statusBadge($incident['status']) == 'badge-success' ? 'bg-green-500' : 'bg-cyan-400')) ?>">
                        <?= h($incident['status']) ?>
                    </span>
                </div>
                <div class="mb-3">
                    <span class="text-cyan-700 font-semibold">Priority:</span>
                    <span class="inline-block px-3 py-1 rounded-lg font-bold text-white <?= priorityBadge($incident['priority']) == 'badge-danger' ? 'bg-red-500' : (priorityBadge($incident['priority']) == 'badge-warning' ? 'bg-yellow-400 text-cyan-900' : (priorityBadge($incident['priority']) == 'badge-success' ? 'bg-green-500' : (priorityBadge($incident['priority']) == 'badge-dark' ? 'bg-gray-800' : 'bg-cyan-400'))) ?>">
                        <?= h($incident['priority']) ?>
                    </span>
                </div>
                <div class="mb-3">
                    <span class="text-cyan-700 font-semibold">Category:</span>
                    <span><?= h($incident['name']) ?></span>
                </div>
                <div class="mb-3">
                    <span class="text-cyan-700 font-semibold">Branch ID:</span>
                    <span><?= h($incident['branch_id']) ?></span>
                </div>
                <div class="mb-3">
                    <span class="text-cyan-700 font-semibold">Assigned To:</span>
                    <span><?= h($incident['assigned_to_name']) ?></span>
                </div>
                <div class="mb-3">
                    <span class="text-cyan-700 font-semibold">Submitted By:</span>
                    <span><?= h($incident['submitted_by_name']) ?></span>
                </div>
            </div>
            <div>
                <div class="mb-3">
                    <span class="text-cyan-700 font-semibold">Created At:</span>
                    <span><?= h($incident['created_at']) ?></span>
                </div>
                <div class="mb-3">
                    <span class="text-cyan-700 font-semibold">Assigned Date:</span>
                    <span><?= h($incident['assigned_date']) ?></span>
                </div>
                <div class="mb-3">
                    <span class="text-cyan-700 font-semibold">Fixed Date:</span>
                    <span><?= h($incident['fixed_date']) ?></span>
                </div>
                <div class="mb-3">
                    <span class="text-cyan-700 font-semibold">Saved Amount:</span>
                    <span><?= h($incident['saved_amount']) ?></span>
                </div>
                <div class="mb-3">
                    <span class="text-cyan-700 font-semibold">Remark:</span>
                    <span><?= h($incident['remark']) ?></span>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <div class="text-cyan-700 font-semibold mb-1">Description:</div>
            <div class="bg-cyan-50 rounded-lg p-4 text-cyan-900 shadow-inner"><?= nl2br(h($incident['description'])) ?></div>
        </div>

        <div class="mb-8 text-center">
            <?php if (!empty($incident['filepath']) && file_exists($incident['filepath'])): ?>
                <span class="text-cyan-700 font-semibold">Attached Image</span>
                <a href="<?= h($incident['filepath']) ?>" target="_blank">
                    <img src="<?= h($incident['filepath']) ?>" alt="Incident Image" class="mx-auto rounded-xl shadow-lg border-4 border-cyan-100 mt-2" style="max-width: 350px; max-height: 250px; object-fit: contain;">
                </a>
            <?php else: ?>
                <div class="text-rose-700 italic">No image attached.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>