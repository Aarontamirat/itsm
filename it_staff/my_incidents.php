<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit;
}

$staff_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT i.*, u.name AS submitted_by_name 
                       FROM incidents i 
                       JOIN users u ON i.submitted_by = u.id 
                       WHERE i.assigned_to = ? 
                       ORDER BY i.created_at DESC");
$stmt->execute([$staff_id]);
$incidents = $stmt->fetchAll();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['incident_id'], $_POST['status'])) {
    $incident_id = (int)$_POST['incident_id'];
    $status = $_POST['status'];

    if (!in_array($status, ['Pending', 'Fixed', 'Not Fixed'])) {
        $_SESSION['error'] = "Invalid status.";
    } else {
        $update = $pdo->prepare("UPDATE incidents SET status = ? WHERE id = ?");
        $update->execute([$status, $incident_id]);

        $log = $pdo->prepare("INSERT INTO incident_logs (incident_id, action, user_id, created_at) VALUES (?, ?, ?, NOW())");
        $log->execute([$incident_id, "Status changed to $status", $staff_id]);

        $_SESSION['success'] = "Incident status updated.";
    }

    header("Location: my_incidents.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Assigned Incidents</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

<!-- header and sidebar -->
      <?php include '../includes/sidebar.php'; ?>
    <?php include '../header.php'; ?>


    <div class="max-w-7xl ms-auto bg-white p-6 mt-4 shadow rounded">
        <h2 class="text-2xl font-bold mb-4">My Assigned Incidents</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 text-red-800 p-3 mb-4 rounded">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (count($incidents) === 0): ?>
            <p>No incidents assigned to you currently.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full border">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="p-2 border">Title</th>
                            <th class="p-2 border">description</th>
                            <th class="p-2 border">Submitted By</th>
                            <th class="p-2 border">Priority</th>
                            <th class="p-2 border">Status</th>
                            <th class="p-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($incidents as $incident): ?>
                            <tr class="text-sm border-b">
                                <td class="p-2"><?= htmlspecialchars($incident['title']) ?></td>
                                <td class="p-2"><?= htmlspecialchars($incident['description']) ?></td>
                                <td class="p-2"><?= htmlspecialchars($incident['submitted_by_name']) ?></td>
                                <td class="p-2"><?= htmlspecialchars($incident['priority']) ?></td>
                                <td class="p-2"><?= htmlspecialchars($incident['status']) ?></td>
                                <td class="p-2">
                                    <form method="POST" class="flex items-center gap-2">
                                        <input type="hidden" name="incident_id" value="<?= $incident['id'] ?>">
                                        <select name="status" class="border p-1 text-sm rounded">
                                            <option value="Pending" <?= $incident['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Fixed" <?= $incident['status'] === 'Fixed' ? 'selected' : '' ?>>Fixed</option>
                                            <option value="Not Fixed" <?= $incident['status'] === 'Not Fixed' ? 'selected' : '' ?>>Not Fixed</option>
                                        </select>
                                        <div class="flex justify-between items-center gap-2">
                                            <button type="submit" class="bg-blue-600 text-white px-2 py-1 rounded text-sm">Update</button>
                                            <a href="faq_submit.php?incident=<?= $incident['id'] ?>" class="bg-green-600 text-white px-2 py-1 rounded text-sm">Solution</a>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
