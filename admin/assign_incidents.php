<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}


// Fetch all unassigned incidents
$stmt = $pdo->query(
"SELECT 
    i.*, 
    u.name AS submitted_by_name,
    b.name AS branch_name
FROM 
    incidents i
JOIN 
    users u ON i.submitted_by = u.id
JOIN 
    branches b ON i.branch_id = b.id
WHERE 
    i.assigned_to IS NULL
ORDER BY 
    i.created_at DESC;"
);

$incidents = $stmt->fetchAll();

// Fetch all IT Staff
$staff_stmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'staff'");
$staff_stmt->execute();
$it_staff = $staff_stmt->fetchAll();

// Handle assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['incident_id'], $_POST['staff_id'])) {
    $incident_id = (int)$_POST['incident_id'];
    $staff_id = (int)$_POST['staff_id'];

    $update = $pdo->prepare("UPDATE incidents SET assigned_to = ?, assigned_date = NOW(), status = 'assigned' WHERE id = ?");
    $update->execute([$staff_id, $incident_id]);

    // Add to incident logs
    // fetch the IT Staff name where the staff_id is equal to the staff_id
    $log = $pdo->prepare("SELECT name FROM users WHERE id = ?");
    $log->execute([$staff_id]);
    $staff_name = $log->fetchColumn();

    // Log the action
    $log = $pdo->prepare("INSERT INTO incident_logs (incident_id, action, user_id, created_at) VALUES (?, ?, ?, NOW())");
    $log->execute([$incident_id, "Assigned to IT Staff (User ID: $staff_name)", $_SESSION['user_id']]);

    // update noitifications table
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, related_incident_id) VALUES (?, ?, ?)");
    $stmt->execute([$staff_id, "You have been assigned to an incident", $incident_id]);

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

<body class="bg-gray-100">

    <!-- header and sidebar -->
      <?php include '../includes/sidebar.php'; ?>
    <?php include '../header.php'; ?>

    <div class="max-w-6xl ms-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 pt-20 fade-in tech-border glow mt-8">
        <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Assign Incidents</h2>
        <p class="text-center text-cyan-500 mb-1 font-mono">Assign unassigned incidents to IT Staff</p>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div id="success-message" class="mb-4 text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
                <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
            <script>
                setTimeout(function() {
                    var el = document.getElementById('success-message');
                    if (el) el.style.opacity = '1';
                }, 10);
                setTimeout(function() {
                    var el = document.getElementById('success-message');
                    if (el) el.style.opacity = '0';
                }, 3010);
            </script>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div id="error-message" class="mb-4 text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
            <script>
                setTimeout(function() {
                    var el = document.getElementById('error-message');
                    if (el) el.style.opacity = '1';
                }, 10);
                setTimeout(function() {
                    var el = document.getElementById('error-message');
                    if (el) el.style.opacity = '0';
                }, 3010);
            </script>
        <?php endif; ?>

        <div class="overflow-x-auto rounded-xl shadow-inner mt-8">
            <?php if (count($incidents) === 0): ?>
                <p class="text-center text-cyan-600 font-mono text-lg py-8">No unassigned incidents at the moment.</p>
            <?php else: ?>
                <table class="w-full border border-cyan-100 bg-white bg-opacity-90 font-mono text-cyan-900">
                    <thead>
                        <tr class="bg-cyan-50 text-cyan-700 text-left">
                            <th class="p-3 font-bold">Title</th>
                            <th class="p-3 font-bold">Submitted By</th>
                            <th class="p-3 font-bold">Branch</th>
                            <th class="p-3 font-bold">Priority</th>
                            <th class="p-3 font-bold">Created</th>
                            <th class="p-3 font-bold">Assign To</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($incidents as $incident): ?>
                            <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition">
                                <td class="p-3"><?= htmlspecialchars($incident['title']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($incident['submitted_by_name']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($incident['branch_name']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($incident['priority']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($incident['created_at']) ?></td>
                                <td class="p-3">
                                    <form method="POST" class="flex flex-col md:flex-row gap-2 items-center">
                                        <input type="hidden" name="incident_id" value="<?= $incident['id'] ?>">
                                        <select name="staff_id" class="border border-cyan-200 bg-cyan-50 px-3 py-2 rounded-lg text-sm font-mono focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200" required>
                                            <option value="">Select IT Staff</option>
                                            <?php foreach ($it_staff as $staff): ?>
                                                <option value="<?= $staff['id'] ?>"><?= htmlspecialchars($staff['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit"
                                            class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-4 py-2 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
                                            Assign
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>