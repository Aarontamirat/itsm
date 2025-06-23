<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit;
}

$staff_id = $_SESSION['user_id'];
            // Build dynamic WHERE clause and parameters for search filters
            $where = ["i.assigned_to = ?"];
            $params = [$staff_id];

            // Title filter
            if (isset($_GET['title']) && $_GET['title'] !== '') {
                $where[] = "i.title LIKE ?";
                $params[] = '%' . $_GET['title'] . '%';
            }

            // Status filter
            if (isset($_GET['statuss']) && $_GET['statuss'] !== '') {
                $where[] = "i.status = ?";
                $params[] = $_GET['statuss'];
            }

            // Branch filter
            if (isset($_GET['branch_id']) && $_GET['branch_id'] !== '') {
                $where[] = "i.branch_id = ?";
                $params[] = $_GET['branch_id'];
            }

            // Submitter filter
            if (isset($_GET['submitted_by']) && $_GET['submitted_by'] !== '') {
                $where[] = "i.submitted_by = ?";
                $params[] = $_GET['submitted_by'];
            }

            // Date range filter
            if (!empty($_GET['date_from'])) {
                $where[] = "DATE(i.created_at) >= ?";
                $params[] = $_GET['date_from'];
            }
            if (!empty($_GET['date_to'])) {
                $where[] = "DATE(i.created_at) <= ?";
                $params[] = $_GET['date_to'];
            }

            $whereSql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            // Pagination settings
            $perPage = 10;
            $totalStmt = $pdo->prepare(
                "SELECT COUNT(*) FROM incidents i $whereSql"
            );
            $totalStmt->execute($params);
            $totalIncidents = (int)$totalStmt->fetchColumn();
            $totalPages = max(1, ceil($totalIncidents / $perPage));
            $page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $perPage;

            // Fetch paginated incidents with all filters
            $limit = (int)$perPage;
            $offsetInt = (int)$offset;
            $sql = "SELECT i.*, u.name AS submitted_by_name, c.name AS name 
                 FROM incidents i 
                 LEFT JOIN users u ON i.submitted_by = u.id
                 LEFT JOIN kb_categories c ON i.category_id = c.id 
                 $whereSql
                 ORDER BY i.created_at DESC
                 LIMIT $limit OFFSET $offsetInt";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $incidents = $stmt->fetchAll();


// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['incident_id'], $_POST['status'], $_POST['saved_amount'])) {
    $incident_id = (int)$_POST['incident_id'];
    $status = $_POST['status'];
    $saved_amount = $_POST['saved_amount'];
    $remark = isset($_POST['remark']) ? trim($_POST['remark']) : '';

    if (!in_array($status, ['pending', 'fixed', 'not fixed', 'support'])) {
        $_SESSION['error'] = "Invalid status.";
    } else {

        // track update status
        $updated = false;

        // fetch the current status of the incident
        $stmt = $pdo->prepare("SELECT status FROM incidents WHERE id = ?");
        $stmt->execute([$incident_id]);
        $incid = $stmt->fetchColumn();

        // set status of incident
        if ($status === 'fixed') {
            // when fixed
            $update = $pdo->prepare("UPDATE incidents SET status = ?, fixed_date = NOW(), saved_amount = ?, remark = ? WHERE id = ?");
            if($update->execute([$status, $saved_amount, $remark, $incident_id])){
                $saved_amount = true;
                $updated = true;
            }
        } elseif ($status === 'support') {
            // when support needed set assigned_to and assigned_date to null in the database
            $update = $pdo->prepare("UPDATE incidents SET status = ?, assigned_to = NULL, assigned_date = NULL, remark = ? WHERE id = ?");
            if($update->execute([$status, $remark, $incident_id])){
                $saved_amount = true;
                $updated = true;
            }
            // If previously fixed, reset fixed_date to null
            if ($incid === 'fixed') {
                $resetFixedDate = $pdo->prepare("UPDATE incidents SET fixed_date = NULL, saved_amount = NULL, remark = NULL WHERE id = ?");
                if($resetFixedDate->execute([$incident_id])) {
                $saved_amount = true;
                $updated = true;
            }
            }
        } else {
            // when not fixed
            $update = $pdo->prepare("UPDATE incidents SET status = ? WHERE id = ?");
            if($update->execute([$status, $incident_id])) {
                $saved_amount = true;
                $updated = true;
            }
            // If previously fixed, reset fixed_date to null
            if ($incid === 'fixed') {
                $resetFixedDate = $pdo->prepare("UPDATE incidents SET fixed_date = NULL, saved_amount = NULL, remark = NULL WHERE id = ?");
                if($resetFixedDate->execute([$incident_id])) {
                $saved_amount = true;
                $updated = true;
            }
            }
        }


        // After updating the incident status:
        if ($updated == true) {

            // Fetch the user who created the incident
            $stmtUser = $pdo->prepare("SELECT submitted_by FROM incidents WHERE id = ?");
            $stmtUser->execute([$incident_id]);
            $incidentUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

            // if need support is selected, notify the user who submitted the incident
            if ($status === 'support' && $incidentUser) {
                $userId = $incidentUser['submitted_by'];
                $message = $_SESSION['name'] . ' asked for support for incident ' . $incident_id;
                
                // Insert into notifications for admins
                $admins = $pdo->query("SELECT id FROM users WHERE role = 'admin'")->fetchAll();
                foreach ($admins as $admin) {
                    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, related_incident_id) VALUES (?, ?, ?)");
                    $stmt->execute([$admin['id'], $message, $incident_id]);
                }

            } elseif ($status !== 'support' && $incidentUser) {
                $userId = $incidentUser['submitted_by'];
                $message = "Your incident (ID: $incident_id) has been marked as $status.";

                // Insert into notifications
                $stmtNotif = $pdo->prepare("INSERT INTO notifications (user_id, message, related_incident_id, is_seen, created_at) VALUES (?, ?, ?, 0, NOW())");
                $stmtNotif->execute([$userId, $message, $incident_id]);
            } else {
                $_SESSION['error'] = "Incident user not found.";
            }
        }

        $log = $pdo->prepare("INSERT INTO incident_logs (incident_id, action, user_id, created_at) VALUES (?, ?, ?, NOW())");
        $log->execute([$incident_id, "Status changed to $status by {$_SESSION['name']}", $staff_id]);

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

<div class="max-w-7xl ms-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 pt-20 fade-in tech-border glow mt-8">
    <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">My Assigned Incidents</h2>
    <p class="text-center text-cyan-500 mb-6 font-mono">Manage and update your assigned incidents</p>

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

    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <form method="get" class="flex flex-col md:flex-row items-center gap-2 w-full md:w-auto">
            <!-- Incident Title -->
            <input type="text" name="title" placeholder="Search by Incident Title" value="<?= isset($_GET['title']) ? htmlspecialchars($_GET['title']) : '' ?>"
            class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono w-full md:w-48" />

            <!-- Status Filter -->
            <select name="statuss" class="px-3 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 font-mono w-full md:w-36">
            <option value="">All Status</option>
            <?php
            $statuses = ['pending', 'fixed', 'not fixed', 'support', 'assigned', 'rejected', 'fixed_confirmed'];
            foreach ($statuses as $statusOpt):
            ?>
                <option value="<?= $statusOpt ?>" <?= (isset($_GET['statuss']) && $_GET['statuss'] === $statusOpt) ? 'selected' : '' ?>>
                <?= ucfirst(str_replace('_', ' ', $statusOpt)) ?>
                </option>
            <?php endforeach; ?>
            </select>

            <!-- Branch Filter -->
            <select name="branch_id" class="px-3 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 font-mono w-full md:w-36">
            <option value="">All Branches</option>
            <?php
            $branches = $pdo->query("SELECT id, name FROM branches ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($branches as $branch):
            ?>
                <option value="<?= $branch['id'] ?>" <?= (isset($_GET['branch_id']) && $_GET['branch_id'] == $branch['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($branch['name']) ?>
                </option>
            <?php endforeach; ?>
            </select>

            <!-- Submitter Filter -->
            <select name="submitted_by" class="px-3 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 font-mono w-full md:w-36">
            <option value="">All Submitters</option>
            <?php
            $submitters = $pdo->query("SELECT DISTINCT u.id, u.name FROM users u INNER JOIN incidents i ON i.submitted_by = u.id ORDER BY u.name ASC")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($submitters as $submitter):
            ?>
                <option value="<?= $submitter['id'] ?>" <?= (isset($_GET['submitted_by']) && $_GET['submitted_by'] == $submitter['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($submitter['name']) ?>
                </option>
            <?php endforeach; ?>
            </select>

            <!-- Date Range -->
            <input type="date" name="date_from" value="<?= isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : '' ?>"
            class="px-3 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 font-mono w-full md:w-36" placeholder="From" />
            <input type="date" name="date_to" value="<?= isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : '' ?>"
            class="px-3 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 font-mono w-full md:w-36" placeholder="To" />

            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest">
            Search
            </button>
        </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        $search = isset($_GET['id']) ? '%' . htmlspecialchars($_GET['id']) . '%' : '';
        $stmt = $pdo->prepare(
            "SELECT i.*, u.name AS submitted_by_name, c.name AS name 
               FROM incidents i 
               LEFT JOIN users u ON i.submitted_by = u.id
               LEFT JOIN kb_categories c ON i.category_id = c.id 
               WHERE i.id LIKE ? AND i.assigned_to = ?
               ORDER BY i.created_at DESC");
        $stmt->execute([$search, $staff_id]);
        $incidents = $stmt->fetchAll();
    }
    ?>

    <div class="overflow-x-auto rounded-xl shadow-inner">
        <?php if (count($incidents) === 0): ?>
            <p class="text-center text-cyan-600 font-mono py-8">No incidents assigned to you currently.</p>
        <?php else: ?>
            <div class="w-full overflow-x-auto">
                <table class="min-w-[900px] w-full border border-cyan-100 bg-white bg-opacity-90 font-mono text-cyan-900">
                    <thead>
                        <tr class="bg-cyan-50 text-cyan-700 text-left">
                            <th class="p-3 font-bold whitespace-nowrap">Title</th>
                            <th class="p-3 font-bold whitespace-nowrap">Description</th>
                            <th class="p-3 font-bold whitespace-nowrap">Submitted By</th>
                            <th class="p-3 font-bold whitespace-nowrap">Category</th>
                            <th class="p-3 font-bold whitespace-nowrap">Priority</th>
                            <th class="p-3 font-bold whitespace-nowrap">Status</th>
                            <th class="p-3 font-bold whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($incidents as $incident): ?>
                            <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition cursor-pointer"
                                onclick="window.location.href='incident_view.php?id=<?= $incident['id'] ?>'">
                                <td class="p-3 max-w-xs truncate" title="<?= htmlspecialchars($incident['title']) ?>">
                                    <?= htmlspecialchars($incident['title']) ?>
                                </td>
                                <td class="p-3 max-w-xs truncate" title="<?= htmlspecialchars($incident['description']) ?>">
                                    <?= htmlspecialchars($incident['description']) ?>
                                </td>
                                <td class="p-3 whitespace-nowrap"><?= htmlspecialchars($incident['submitted_by_name']) ?></td>
                                <td class="p-3 whitespace-nowrap"
                                    onclick="event.stopPropagation();">
                                    <!-- category -->
                                    <form method="POST" action="change_category.php" class="flex flex-col md:flex-row items-center gap-1">
                                        <input type="hidden" name="incident_id" value="<?= $incident['id'] ?>">
                                        <select name="category_id" class="border p-2 text-sm rounded-lg font-mono bg-cyan-50 border-cyan-200 focus:ring-2 focus:ring-cyan-300 transition"
                                            onchange="this.form.submit()">
                                            <?php
                                            // Fetch all categories
                                            $catStmt = $pdo->query("SELECT id, name FROM kb_categories ORDER BY name ASC");
                                            $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($categories as $cat):
                                            ?>
                                                <option value="<?= $cat['id'] ?>" <?= $incident['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </td>
                                <td class="p-3 whitespace-nowrap"><?= htmlspecialchars($incident['priority']) ?></td>
                                <td class="p-3 capitalize whitespace-nowrap">
                                    <?php 
                                // UI green for fixed, red for pending and dull gray for rejected.
                                if ($incident['status'] === 'fixed') {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-green-100 text-green-700 font-semibold">Fixed</span>';
                                } elseif ($incident['status'] === 'fixed_confirmed') {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-green-300 text-white font-semibold">Fixed Confirmed</span>';
                                } elseif ($incident['status'] === 'pending') {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-red-100 text-red-700 font-semibold animate-pulse">Pending</span>';
                                } elseif ($incident['status'] === 'not fixed') {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-orange-500 text-white font-semibold">Unfixed</span>';
                                } elseif ($incident['status'] === 'assigned') {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-yellow-200 text-gray-500 font-semibold">Assigned</span>';
                                } elseif ($incident['status'] === 'rejected') {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-gray-200 text-gray-500 font-semibold">Rejected</span>';
                                } else {
                                    echo '<span class="inline-block px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 font-semibold">' . htmlspecialchars($incident['status']) . '</span>';
                                } 
                                ?>
                                </td>
                                <td class="p-3 whitespace-nowrap"
                                    onclick="event.stopPropagation();">
                                    <!-- status -->
                                     <div class="flex flex-col md:flex-row items-center gap-2">
                                        <?php
                                        if ($incident['status'] !== 'fixed_confirmed') {
                                        ?>
                                        <form method="POST" class="flex flex-col md:flex-row items-center gap-2">
                                            <input type="hidden" name="incident_id" value="<?= $incident['id'] ?>">
                                            <select name="status" class="border p-2 text-sm rounded-lg font-mono bg-cyan-50 border-cyan-200 focus:ring-2 focus:ring-cyan-300 transition">
                                                <option value="pending" <?= strtolower($incident['status']) === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="fixed" <?= strtolower($incident['status']) === 'fixed' ? 'selected' : '' ?>>Fixed</option>
                                                <option value="not fixed" <?= strtolower($incident['status']) === 'not fixed' ? 'selected' : '' ?>>Not Fixed</option>
                                                <option class="bg-red-500 text-white" value="support" <?= strtolower($incident['status']) === 'support' ? 'selected' : '' ?>>Need Support</option>
                                            </select>
                                            <input type="number" name="saved_amount" step="0.01" min="0" class="border p-2 rounded-lg font-mono w-32 bg-cyan-50 border-cyan-200 focus:ring-2 focus:ring-cyan-300 transition" placeholder="Estimated cost">
                                            <button type="submit" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-4 py-2 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
                                                Update
                                            </button>
                                        </form>
                                        <?php } ?>

                                        <div class="flex flex-col md:flex-row items-center gap-2">
                                            <a href="kb_list.php" class="bg-green-500 hover:bg-green-600 text-white font-bold rounded-lg shadow px-4 py-2 font-mono transition">Solution</a>
                                            <?php
                                                // Fetch image path for this incident
                                                $imgStmt = $pdo->prepare("SELECT `filepath` FROM files WHERE incident_id = ? LIMIT 1");
                                                $imgStmt->execute([$incident['id']]);
                                                $imgPath = $imgStmt->fetchColumn();
                                                if ($imgPath):
                                            ?>
                                                <a href="<?= htmlspecialchars($imgPath) ?>" target="_blank" class="bg-cyan-700 hover:bg-cyan-800 text-white font-bold rounded-lg shadow px-4 py-2 font-mono transition">Image</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Modal for entering remark when status is set to 'fixed' -->
            <div id="remark-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full">
                    <h3 class="text-xl font-bold mb-4 text-cyan-700 font-mono">Add Remark</h3>
                    <form id="remark-form" class="flex flex-col gap-4">
                        <input type="hidden" name="incident_id" id="modal-incident-id">
                        <input type="hidden" name="status" value="fixed">
                        <input type="hidden" name="saved_amount" id="modal-saved-amount">
                        <label class="font-mono text-cyan-700">Remark:</label>
                        <textarea name="remark" id="modal-remark" rows="3" required class="border rounded-lg p-2 font-mono bg-cyan-50 border-cyan-200 focus:ring-2 focus:ring-cyan-300"></textarea>
                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="closeRemarkModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-mono hover:bg-gray-300">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-cyan-600 text-white rounded-lg font-mono hover:bg-cyan-700">Submit</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
            document.querySelectorAll('form').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    // Only intercept forms that update status
                    if (form.querySelector('select[name="status"]')) {
                        var status = form.querySelector('select[name="status"]').value;

                        // when fixed
                        if (status === 'fixed') {
                            e.preventDefault();
                            // Show modal and fill hidden fields
                            document.getElementById('remark-modal').classList.remove('hidden');
                            document.getElementById('modal-incident-id').value = form.querySelector('input[name="incident_id"]').value;
                            document.getElementById('modal-saved-amount').value = form.querySelector('input[name="saved_amount"]').value;
                            // Store reference to the original form for later
                            window._originalFormFixed = form;
                        }

                        // when need support
                        if (status === 'support') {
                            e.preventDefault();
                            // Show modal and fill hidden fields
                            document.getElementById('remark-modal').classList.remove('hidden');
                            document.getElementById('modal-incident-id').value = form.querySelector('input[name="incident_id"]').value;
                            // Store reference to the original form for later
                            window._originalFormSupport = form;
                        }
                    }
                });
            });

            // Modal form submit
            document.getElementById('remark-form').addEventListener('submit', function(e) {
                e.preventDefault();
                // Create a new form and submit with all original + remark fields
                var origFormFixed = window._originalFormFixed;
                var origFormSupport = window._originalFormSupport;
                if (origFormFixed) {
                    var formData = new FormData(origFormFixed);
                    formData.set('status', 'fixed');
                    formData.set('incident_id', document.getElementById('modal-incident-id').value);
                    formData.set('saved_amount', document.getElementById('modal-saved-amount').value);
                    formData.append('remark', document.getElementById('modal-remark').value);

                    // Create a temporary form to submit
                    var tempForm = document.createElement('form');
                    tempForm.method = 'POST';
                    tempForm.action = origFormFixed.action || '';
                    for (var [key, value] of formData.entries()) {
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = value;
                        tempForm.appendChild(input);
                    }
                    document.body.appendChild(tempForm);
                    tempForm.submit();
                } else if (origFormSupport) {
                    var formData = new FormData(origFormSupport);
                    formData.set('status', 'support');
                    formData.set('incident_id', document.getElementById('modal-incident-id').value);
                    formData.append('remark', document.getElementById('modal-remark').value);

                    // Create a temporary form to submit
                    var tempForm = document.createElement('form');
                    tempForm.method = 'POST';
                    tempForm.action = origFormSupport.action || '';
                    for (var [key, value] of formData.entries()) {
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = value;
                        tempForm.appendChild(input);
                    }
                    document.body.appendChild(tempForm);
                    tempForm.submit();
                } else {
                    return;
                }
            });

            // Close modal
            function closeRemarkModal() {
                document.getElementById('remark-modal').classList.add('hidden');
                document.getElementById('modal-remark').value = '';
            }
            </script>

            <?php
            // Pagination controls
            if ($totalPages > 1): ?>
                <div class="flex justify-center mt-6 mb-2">
                    <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
                        <?php if ($page > 1): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>"
                               class="px-3 py-2 border border-cyan-200 bg-cyan-50 text-cyan-700 hover:bg-cyan-100 rounded-l-md font-mono">Prev</a>
                        <?php else: ?>
                            <span class="px-3 py-2 border border-cyan-200 bg-gray-100 text-gray-400 rounded-l-md font-mono cursor-not-allowed">Prev</span>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                               class="px-3 py-2 border-t border-b border-cyan-200 <?= $i === $page ? 'bg-cyan-300 text-white font-bold' : 'bg-cyan-50 text-cyan-700 hover:bg-cyan-100' ?> font-mono"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>"
                               class="px-3 py-2 border border-cyan-200 bg-cyan-50 text-cyan-700 hover:bg-cyan-100 rounded-r-md font-mono">Next</a>
                        <?php else: ?>
                            <span class="px-3 py-2 border border-cyan-200 bg-gray-100 text-gray-400 rounded-r-md font-mono cursor-not-allowed">Next</span>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
