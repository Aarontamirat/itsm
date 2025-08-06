<?php
// /c:/xampp/htdocs/itsm/it_staff/incident_view.php

require_once '../config/db.php';

// Get incident ID from query string
$incident_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($incident_id <= 0) {
    die('<div style="color:red;text-align:center;margin-top:50px;">Invalid Incident ID.</div>');
}

//  Fetch files where incident ID matches
$stmt = $pdo->prepare("SELECT filepath, maintenance_form FROM files WHERE incident_id = ?");
$stmt->execute([$incident_id]);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch incident details
$stmt = $pdo->prepare(
    "SELECT 
        i.*, f.filepath, f.maintenance_form, u2.name AS assigned_to_name, u.name AS submitted_by_name, c.name AS name
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
            <!-- incident file attachment -->
            <?php if (!empty($files)): ?>
                <div class="mb-4">
                    <span class="text-cyan-700 font-semibold text-lg block mb-2">
                        <svg class="inline-block w-6 h-6 mr-1 text-cyan-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15.172 7l-6.586 6.586a2 2 0 002.828 2.828L18 9.828a4 4 0 00-5.656-5.656L5.343 11.172a6 6 0 108.485 8.485"></path></svg>
                        Attached Files
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mt-2">
                        <?php foreach ($files as $file): ?>
                            <?php if (!empty($file['filepath']) && file_exists($file['filepath'])): ?>
                                <div class="bg-cyan-50 rounded-xl shadow-lg border border-cyan-200 p-4 flex flex-col items-center hover:shadow-2xl transition duration-200">
                                    <?php if (empty($file['maintenance_form']) || $file['maintenance_form'] == 0): ?>
                                        <span class="text-cyan-800 font-semibold mb-2 flex items-center">
                                            <svg class="w-5 h-5 mr-1 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16V4a2 2 0 012-2h12a2 2 0 012 2v12"></path><path d="M4 16l8 5 8-5"></path></svg>
                                            Incident File
                                        </span>
                                        <a href="<?= h($file['filepath']) ?>" target="_blank" class="block">
                                            <img src="<?= h($file['filepath']) ?>" alt="Incident File" class="rounded-lg shadow-md border-2 border-cyan-100 hover:border-cyan-400 transition duration-200" style="max-width: 250px; max-height: 180px; object-fit: contain;">
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($file['maintenance_form']) && $file['maintenance_form'] == 1): ?>
                                        <span class="text-cyan-800 font-semibold mb-2 flex items-center">
                                            <svg class="w-5 h-5 mr-1 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 17l4 4 4-4"></path><path d="M12 3v18"></path></svg>
                                            Maintenance Form
                                        </span>
                                        <a href="<?= h($file['filepath']) ?>" target="_blank" class="text-blue-600 hover:underline font-medium">
                                            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                                            View Maintenance Form
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>