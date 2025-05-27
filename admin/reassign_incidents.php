<?php
session_start();
require_once '../config/db.php';

// Restrict to Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch the incident ID from the form submission
if (isset($_GET['id'])) {
    $incident_id = (int)$_GET['id'];

    // Fetch the incident details
    $stmt = $pdo->prepare("SELECT * FROM incidents WHERE id = ?");
    $stmt->execute([$incident_id]);
    $incident = $stmt->fetch();

    // Fetch all IT Staff
    $staff_stmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'staff'");
    $staff_stmt->execute();
    $it_staff = $staff_stmt->fetchAll();

    // Handle reassignment
    if (isset($_POST['staff_id'])) {
        $staff_id = (int)$_POST['staff_id'];
        
        $status = 'assigned';
        // Update the incident with the new staff ID
        $update_stmt = $pdo->prepare("UPDATE incidents SET status = ?, assigned_to = ?, assigned_date = NOW() WHERE id = ?");
        if ($update_stmt->execute([$status, $staff_id, $incident_id])) {


            // update noitifications table
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, related_incident_id) VALUES (?, ?, ?)");
            $stmt->execute([$staff_id, "You have been assigned to an incident", $incident_id]);


            // Add to incident logs
            // fetch the IT Staff name where the staff_id is equal to the staff_id
            $log = $pdo->prepare("SELECT name FROM users WHERE id = ?");
            $log->execute([$staff_id]);
            $staff_name = $log->fetchColumn();
            
            // Log the action
            $log = $pdo->prepare("INSERT INTO incident_logs (incident_id, action, user_id, created_at) VALUES (?, ?, ?, NOW())");
            $log->execute([$incident_id, "Assigned to IT Staff ($staff_name)", $_SESSION['user_id']]);

            $_SESSION['success'] = "Incident reassigned successfully.";
        } else {
            $_SESSION['error'] = "Failed to reassign incident.";
        }
    }

    if (!$incident) {
        $_SESSION['error'] = "Incident not found.";
        header("header(refresh:2;url= reassign_incidents.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: incidents.php");
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

<?php
require_once '../includes/sidebar.php';
require_once '../header.php';
?>

    <div class="max-w-5xl mx-auto bg-white p-6 mt-4 shadow rounded">
        <h2 class="text-2xl font-bold mb-4">Reassign Incident</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">
                <?= $_SESSION['success'];
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (!$incident): ?>
            <p>Cannot find the incident, please go back and refresh the page.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full border">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="p-2 border">Title</th>
                            <th class="p-2 border">Priority</th>
                            <th class="p-2 border">Created</th>
                            <th class="p-2 border">Reassign To</th>
                        </tr>
                    </thead>
                    <tbody>
                            <tr class="text-sm border-b">
                                <td class="p-2"><?= htmlspecialchars($incident['title']) ?></td>
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
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>