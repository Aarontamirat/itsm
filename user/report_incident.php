<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    $_SESSION['error'] = "Access denied. Please log in.";
    header("Location: ../login.php");
    exit;
}

$errors = [];

// auto assign
function autoAssignITStaff(PDO $pdo, $branchId) {
    $stmt = $pdo->prepare("
        SELECT u.id, COUNT(i.id) as workload
        FROM users u
        LEFT JOIN incidents i ON i.assigned_to = u.id AND i.status IN ('pending', 'assigned')
        INNER JOIN staff_branch_assignments sba ON sba.staff_id = u.id
        WHERE sba.branch_id = ? AND u.role = 'staff'
        GROUP BY u.id
        ORDER BY workload ASC
        LIMIT 1
    ");
    $stmt->execute([$branchId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $priority = $_POST['priority'];
    $user_id = $_SESSION['user_id'];
    $branch_id = $_SESSION['branch_id'];
    $category_id = $_POST['category_id'];
    // auto assignment IT Staff
    $autoAssigned = autoAssignITStaff($pdo, $branch_id);
    $assignedTo = $autoAssigned ? $autoAssigned['id'] : null;

    if (empty($title)) $errors[] = 'Title is required.';
    if (empty($description)) $errors[] = 'Description is required.';
    if (!in_array($priority, ['Low', 'Medium', 'High'])) $errors[] = 'Invalid priority.';
    if (empty($branch_id)) $errors[] = 'Unknown branch, please contact your system.';
    if (empty($category_id)) $errors[] = 'Please select an incident category.';

    if (empty($errors)) {

        // Insert incident
        if($assignedTo === null) {
            $stmt = $pdo->prepare("INSERT INTO incidents (title, description, category_id, priority, status, submitted_by, branch_id, created_at) VALUES (?, ?, ?, ?, 'pending', ?, ?, NOW())");
            $stmt->execute([$title, $description, $category_id, $priority, $user_id, $branch_id]);
            $incident_id = $pdo->lastInsertId();
        } else {
            $stmt = $pdo->prepare("INSERT INTO incidents (title, description, category_id, priority, assigned_to, status, submitted_by, branch_id, assigned_date, created_at) VALUES (?, ?, ?, ?, ?, 'assigned', ?, ?, NOW(), NOW())");
            $stmt->execute([$title, $description, $category_id, $priority, $assignedTo, $user_id, $branch_id]);
            $incident_id = $pdo->lastInsertId();
        }

        // Handle file upload
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_name = basename($_FILES['file']['name']);
            $target_path = "../uploads/" . time() . "_" . $file_name;

            if (move_uploaded_file($file_tmp, $target_path)) {
                $stmt = $pdo->prepare("INSERT INTO files (incident_id, filepath, uploaded_at) VALUES (?, ?, NOW())");
                $stmt->execute([$incident_id, $target_path]);
            }
        }

        // Update notifications for ADMINS
        $admins = $pdo->query("SELECT id FROM users WHERE role = 'admin'")->fetchAll();
        foreach ($admins as $admin) {
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, related_incident_id) VALUES (?, ?, ?)");
            $stmt->execute([$admin['id'], "New incident reported", $incident_id]);
        }
        // update noitifications table for assigned IT_staff
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, related_incident_id) VALUES (?, ?, ?)");
        $stmt->execute([$assignedTo, "You have been assigned to an incident", $incident_id]);



        // Add to incident logs
        $log = $pdo->prepare("INSERT INTO incident_logs (incident_id, action, user_id, created_at) VALUES (?, ?, ?, NOW())");
        $log->execute([$incident_id, "Incident reported by User ID: $user_id", $user_id]);
        
        $message = "Incident submitted successfully!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Report Incident</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<!-- header and sidebar -->
      <?php include '../includes/sidebar.php'; ?>
  <div class="flex-1 ml-20">
    <?php include '../header.php'; ?>

    <div class="max-w-3xl mx-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 pt-16 fade-in tech-border glow mt-8">
        <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Report New Incident</h2>
        <p class="text-center text-cyan-500 mb-6 font-mono">Submit a new IT support incident</p>

        <?php if (!empty($errors)): ?>
            <div id="error-message" class="mb-4 text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
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
        <?php if (isset($message)): ?>
            <div id="success-message" class="mb-4 text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
                <?= $message ?>
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

        <form method="POST" enctype="multipart/form-data" class="space-y-6 mt-6 font-mono">
            <!-- incident title -->
            <div>
                <label class="block text-cyan-700 font-semibold mb-1">Title</label>
                <input type="text" name="title" class="w-full p-3 border border-cyan-200 rounded-lg bg-cyan-50 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition" required>
            </div>

            <!-- incident description -->
            <div>
                <label class="block text-cyan-700 font-semibold mb-1">Description</label>
                <textarea name="description" class="w-full p-3 border border-cyan-200 rounded-lg bg-cyan-50 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition" rows="4" required></textarea>
            </div>

            <!-- incident category -->
            <div>
                <label for="category" class="block text-cyan-700 font-semibold mb-1">Incident Category</label>
                <select name="category_id" id="category" class="block w-full mt-1 p-3 border border-cyan-200 rounded-lg bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition">
                    <option value="">-- Select Category --</option>
                    <?php
                    $stmt = $pdo->query("SELECT id, name FROM kb_categories ORDER BY name ASC");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <!-- incident priority -->
            <div>
                <label class="block text-cyan-700 font-semibold mb-1">Priority</label>
                <select name="priority" class="w-full p-3 border border-cyan-200 rounded-lg bg-cyan-50 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition" required>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>

            <!-- incident file upload -->
            <div>
                <label class="block text-cyan-700 font-semibold mb-1">Optional File Upload</label>
                <input type="file" name="file" class="w-full p-2 border border-cyan-200 rounded-lg bg-cyan-50 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition">
            </div>

            <!-- incident branch -->
            <div>
                <label class="block text-cyan-700 font-semibold mb-1">Branch</label>
                <input type="text" disabled name="branch" value="<?= htmlspecialchars($_SESSION['branch_name']) ?>" class="w-full p-3 border border-cyan-200 rounded-lg bg-cyan-50 text-cyan-900" readonly>
            </div>

            <div class="flex flex-row gap-4 justify-center items-center mt-4">
                <button type="button"
                    onclick="generateMaintenanceForm({
                        title: document.querySelector('[name=title]').value,
                        description: document.querySelector('[name=description]').value,
                        priority: document.querySelector('[name=priority]').value,
                        category: document.querySelector('#category').options[document.querySelector('#category').selectedIndex].text
                    })"
                    class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold rounded-lg px-6 py-2 shadow transition duration-200"
                >
                    Download Maintenance Form PDF
                </button>
                <button type="button"
                    onclick="generateLetterForm({
                        title: document.querySelector('[name=title]').value,
                        description: document.querySelector('[name=description]').value,
                        priority: document.querySelector('[name=priority]').value,
                        category: document.querySelector('#category').options[document.querySelector('#category').selectedIndex].text
                    })"
                    class="bg-green-500 hover:bg-green-600 text-white font-bold rounded-lg px-6 py-2 shadow transition duration-200"
                >
                    Download Letter Form PDF
                </button>
            </div>

            <!-- submit button -->
            <div class="flex flex-col md:flex-row gap-4 justify-center items-center mt-6">
                <button type="submit" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-8 py-3 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
                    Submit Incident
                </button>
                <a href="user_dashboard.php" class="text-cyan-600 hover:underline font-semibold font-mono">Cancel</a>
            </div>
        </form>
    </div>


    <!-- jsPDF CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
    function generateMaintenanceForm(data) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Header
        doc.setFontSize(18);
        doc.text('Lucy Insurance S.C', 105, 18, { align: 'center' });

        // Title
        doc.setFontSize(16);
        doc.text('Maintenance Request Form', 105, 32, { align: 'center' });

        // Date (top right, under title)
        const today = new Date();
        const dateStr = today.toLocaleDateString();
        doc.setFontSize(12);
        doc.text(`Date: ${dateStr}`, 180, 40, { align: 'right' });

        // Table headers (expanded, no Branch)
        let startY = 50;
        doc.setFontSize(12);
        doc.setFillColor(220, 230, 241);
        doc.rect(15, startY, 180, 10, 'F');
        doc.setTextColor(0, 70, 140);
        doc.text('Title', 20, startY + 7);
        doc.text('Description', 60, startY + 7);
        doc.text('Priority', 130, startY + 7);
        doc.text('Category', 155, startY + 7);

        // Calculate dynamic row height based on text size
        const titleLines = doc.splitTextToSize(data.title, 35);
        const descLines = doc.splitTextToSize(data.description, 60);
        const maxLines = Math.max(titleLines.length, descLines.length, 1);
        const rowHeight = maxLines * 7 + 6; // 7px per line, plus padding

        // Table row (expanded columns, dynamic height)
        doc.setTextColor(0, 0, 0);
        startY += 10;
        doc.setFontSize(11);
        doc.rect(15, startY, 180, rowHeight);

        // Draw vertical dividing lines between columns
        // Columns: Title (15-55), Description (55-125), Priority (125-150), Category (150-195)
        doc.line(55, startY, 55, startY + rowHeight);   // Title/Description
        doc.line(125, startY, 125, startY + rowHeight); // Description/Priority
        doc.line(150, startY, 150, startY + rowHeight); // Priority/Category

        // Draw each cell's text, line by line
        let textY = startY + 7;
        for (let i = 0; i < maxLines; i++) {
            doc.text(titleLines[i] || '', 20, textY);
            doc.text(descLines[i] || '', 60, textY);
            if (i === 0) {
            doc.text(data.priority, 130, textY);
            doc.text(data.category, 155, textY);
            }
            textY += 7;
        }

        // Place signature columns near the bottom of the page
        let pageHeight = doc.internal.pageSize.getHeight();
        let sigY = pageHeight - 40; // 40 units from the bottom
        const col1X = 25, col2X = 120;
        doc.setFontSize(12);
        doc.text('Name:', col1X, sigY);
        doc.text('Name:', col2X, sigY);
        doc.line(col1X, sigY + 1, col1X + 70, sigY + 1);
        doc.line(col2X, sigY + 1, col2X + 70, sigY + 1);

        // Signature lines
        doc.text('Signature:', col1X, sigY + 10);
        doc.text('Signature:', col2X, sigY + 10);
        doc.line(col1X, sigY + 11, col1X + 70, sigY + 11);
        doc.line(col2X, sigY + 11, col2X + 70, sigY + 11);

        // Date under signature
        doc.setFontSize(10);
        doc.text(`Date: ${dateStr}`, col1X, sigY + 20);
        doc.text(`Date: ${dateStr}`, col2X, sigY + 20);

        doc.save('maintenance_request_form.pdf');
        }

        // On successful submit, trigger PDF
        <?php if (isset($message)): ?>
        document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            generateMaintenanceForm({
            title: <?= json_encode($title ?? '') ?>,
            description: <?= json_encode($description ?? '') ?>,
            priority: <?= json_encode($priority ?? '') ?>,
            category: <?= json_encode(isset($category_id) ? ($pdo->query("SELECT name FROM kb_categories WHERE id=" . intval($category_id))->fetchColumn() ?: '') : '') ?>,
            });
        }, 500);
        });
        <?php endif; ?>



        // LETTER FORM
        function generateLetterForm(data) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Techy Letterhead
        doc.setFillColor(0, 212, 255);
        doc.rect(0, 0, 210, 18, 'F');
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(22);
        doc.setTextColor(0, 51, 102);
        doc.text('Lucy Insurance S.C', 12, 13);

        // Logo (optional, if you have a base64 image)
        // doc.addImage('data:image/png;base64,...', 'PNG', 170, 3, 30, 12);

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(11);
        doc.setTextColor(30, 41, 59);
        // Fetch branch location from PHP (from database)
        <?php
        // Get branch location from DB using branch_id in session
        $branchLocation = '';
        if (!empty($_SESSION['branch_id'])) {
            $stmt = $pdo->prepare("SELECT location FROM branches WHERE id = ?");
            $stmt->execute([$_SESSION['branch_id']]);
            $branchLocation = $stmt->fetchColumn() ?: '';
        }
        ?>
        doc.text('Branch: ' + <?= json_encode($_SESSION['branch_name'] ?? '') ?>, 12, 22);
        doc.text('Location: ' + <?= json_encode($branchLocation) ?>, 12, 28);

        // Decorative tech lines
        doc.setDrawColor(0, 212, 255);
        doc.setLineWidth(2);
        doc.line(10, 34, 200, 34);
        doc.setDrawColor(0, 51, 102);
        doc.setLineWidth(0.5);
        doc.line(10, 36, 200, 36);

        // Date (top right, techy box)
        const today = new Date();
        const dateStr = today.toLocaleDateString();
        doc.setFillColor(30, 41, 59);
        doc.roundedRect(150, 20, 48, 12, 3, 3, 'F');
        doc.setFont('courier', 'bold');
        doc.setFontSize(11);
        doc.setTextColor(255, 255, 255);
        doc.text(`Date: ${dateStr}`, 154, 28);

        // Recipient (left)
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(13);
        doc.setTextColor(0, 51, 102);
        doc.text('To: IT Support Department', 12, 48);

        // Subject
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(15);
        doc.setTextColor(0, 212, 255);
        doc.text('Subject: Maintenance Request', 12, 60);

        // Salutation
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(12);
        doc.setTextColor(30, 41, 59);
        doc.text('Dear IT Support Team,', 12, 72);

        // Body
        let bodyY = 82;
        const bodyText = [
            "I would like to formally request maintenance support for the following issue:",
            "",
            `Title: ${data.title}`,
            `Description: ${data.description}`,
            `Priority: ${data.priority}`,
            `Category: ${data.category}`,
            "",
            "Please address this request at your earliest convenience.",
            "",
            "Thank you for your prompt attention."
        ];
        doc.setFont('courier', 'normal');
        doc.setFontSize(12);
        doc.setTextColor(30, 41, 59);
        doc.text(doc.splitTextToSize(bodyText.join('\n'), 180), 12, bodyY);

        // Signature block (bottom right, techy)
        let pageHeight = doc.internal.pageSize.getHeight();
        let sigY = pageHeight - 50;
        doc.setDrawColor(0, 212, 255);
        doc.setLineWidth(1.2);
        doc.line(130, sigY + 18, 200, sigY + 18);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(12);
        doc.setTextColor(0, 51, 102);
        doc.text('Sincerely,', 130, sigY);
        doc.setFont('courier', 'normal');
        doc.setFontSize(11);
        doc.setTextColor(30, 41, 59);
        doc.text('Name & Signature', 130, sigY + 26);

        // Footer tech bar
        doc.setFillColor(0, 212, 255);
        doc.rect(0, pageHeight - 12, 210, 12, 'F');
        doc.setFont('courier', 'bold');
        doc.setFontSize(10);
        doc.setTextColor(255, 255, 255);
        doc.text('Lucy Insurance S.C - ITSM Incident Report', 12, pageHeight - 4);

        doc.save('maintenance_request_form.pdf');
        }

        // On successful submit, trigger PDF
        <?php if (isset($message)): ?>
        document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            generateLetterForm({
            title: <?= json_encode($title ?? '') ?>,
            description: <?= json_encode($description ?? '') ?>,
            priority: <?= json_encode($priority ?? '') ?>,
            category: <?= json_encode(isset($category_id) ? ($pdo->query("SELECT name FROM kb_categories WHERE id=" . intval($category_id))->fetchColumn() ?: '') : '') ?>,
            });
        }, 500);
        });
        <?php endif; ?>
    </script>

    

</body>

</html>