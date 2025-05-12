<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch all unassigned incidents
$stmt = $pdo->query("SELECT i.*, u.name AS submitted_by_name 
                     FROM incidents i 
                     JOIN users u ON i.submitted_by = u.id 
                     WHERE i.assigned_to IS NULL 
                     ORDER BY i.created_at DESC");
$incidents = $stmt->fetchAll();

// Fetch all IT Staff
$staff_stmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'staff'");
$staff_stmt->execute();
$it_staff = $staff_stmt->fetchAll();

// Handle assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['incident_id'], $_POST['staff_id'])) {
    $incident_id = (int)$_POST['incident_id'];
    $staff_id = (int)$_POST['staff_id'];

    $update = $pdo->prepare("UPDATE incidents SET assigned_to = ?, status = 'assigned' WHERE id = ?");
    $update->execute([$staff_id, $incident_id]);

    // Add to incident logs
    $log = $pdo->prepare("INSERT INTO incident_logs (incident_id, action, user_id, created_at) VALUES (?, ?, ?, NOW())");
    $log->execute([$incident_id, "Assigned to IT Staff (User ID: $staff_id)", $_SESSION['user_id']]);

    $_SESSION['success'] = "Incident assigned successfully.";
    header("Location: assign_incidents.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Assign Incidents</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-5xl mx-auto bg-white p-6 shadow rounded">
        <h2 class="text-2xl font-bold mb-4">Unassigned Incidents</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">
                <?= $_SESSION['success'];
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (count($incidents) === 0): ?>
            <p>No unassigned incidents at the moment.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full border">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="p-2 border">Title</th>
                            <th class="p-2 border">Submitted By</th>
                            <th class="p-2 border">Priority</th>
                            <th class="p-2 border">Created</th>
                            <th class="p-2 border">Assign To</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($incidents as $incident): ?>
                            <tr class="text-sm border-b">
                                <td class="p-2"><?= htmlspecialchars($incident['title']) ?></td>
                                <td class="p-2"><?= htmlspecialchars($incident['submitted_by_name']) ?></td>
                                <td class="p-2"><?= htmlspecialchars($incident['priority']) ?></td>
                                <td class="p-2"><?= htmlspecialchars($incident['created_at']) ?></td>
                                <td class="p-2">
                                    <form method="POST" class="flex gap-2">
                                        <input type="hidden" name="incident_id" value="<?= $incident['id'] ?>">
                                        <select name="staff_id" class="border p-1 rounded text-sm" required>
                                            <option value="">Select IT Staff</option>
                                            <?php foreach ($it_staff as $staff): ?>
                                                <option value="<?= $staff['id'] ?>"><?= htmlspecialchars($staff['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit"
                                            class="bg-blue-600 text-white px-2 py-1 rounded text-sm">Assign</button>
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