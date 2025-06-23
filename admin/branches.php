<?php
session_start();
require_once '../config/db.php';

// Restrict to Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle feedback messages
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Fetch paginated branches
$limit = 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("SELECT * FROM branches ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$branches = $stmt->fetchAll();

// Count total for pagination
$total = $pdo->query("SELECT COUNT(*) FROM branches")->fetchColumn();
$totalPages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Branches</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <?php include '../includes/sidebar.php'; ?>
    <?php include '../header.php'; ?>

  <div class="max-w-7xl ms-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 fade-in tech-border glow mt-8">
    <h1 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Branch Management</h1>
    <p class="text-center text-cyan-500 mb-1 font-mono">Manage your company branches</p>

    <?php if ($success): ?>
      <div id="success-message" class="mb-4 text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
        <?= htmlspecialchars($success) ?>
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
    <?php elseif ($error): ?>
      <div id="error-message" class="mb-4 text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
        <?= htmlspecialchars($error) ?>
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

      <div class="flex justify-between align-center">
        <!-- Add Branch Modal Trigger -->
        <div class="mb-8 flex justify-end">
          <button id="open-branch-modal"
            class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-500 to-green-400 hover:from-green-400 hover:to-cyan-500 text-white font-bold rounded-lg shadow-lg transition duration-300 font-mono tracking-widest">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Branch
          </button>
        </div>

          <!-- Modal Overlay -->
        <div id="branch-modal-overlay" class="fixed inset-0 bg-black bg-opacity-40 z-40 hidden"></div>

          <!-- Modal -->
        <div id="branch-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
          <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg relative">
          <button id="close-branch-modal" class="absolute top-3 right-3 text-cyan-400 hover:text-cyan-700 text-2xl font-bold">&times;</button>
          <h2 class="text-2xl font-extrabold text-cyan-700 mb-4 text-center font-mono">Add Branch</h2>
          <form action="handlers/create_branch.php" method="POST" class="grid grid-cols-1 gap-6">
          <div>
          <label class="block text-cyan-700 font-semibold mb-1 font-mono" for="name">Branch Name</label>
          <input type="text" name="name" id="name" placeholder="Branch Name" class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono" required>
          </div>
          <div>
          <label class="block text-cyan-700 font-semibold mb-1 font-mono" for="location">Location</label>
          <input type="text" name="location" id="location" placeholder="Location" class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-green-200 focus:outline-none transition duration-200 font-mono" required>
          </div>
          <div>
            <label class="block text-cyan-700 font-semibold mb-1 font-mono" for="phone">Phone Number</label>
            <input type="tel" name="phone" id="phone" placeholder="Phone Number" class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200 font-mono" required>
          </div>
          <div>
            <label class="block text-cyan-700 font-semibold mb-1 font-mono" for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Email Address" class="w-full px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-green-200 focus:outline-none transition duration-200 font-mono" required>
          </div>
          <div class="flex items-center">
            <input type="checkbox" name="is_active" id="is_active" value="1" class="h-5 w-5 text-cyan-600 border-cyan-300 rounded focus:ring-cyan-400" checked>
            <label for="is_active" class="ml-2 text-cyan-700 font-semibold font-mono select-none">Active</label>
          </div>
          <div>
          <button type="submit"
            class="w-full py-2 px-4 bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest">
            Add Branch
          </button>
          </div>
          </form>
          </div>
        </div>

        <script>
          // Modal open/close logic
          const openBtn = document.getElementById('open-branch-modal');
          const closeBtn = document.getElementById('close-branch-modal');
          const modal = document.getElementById('branch-modal');
          const overlay = document.getElementById('branch-modal-overlay');

          openBtn.addEventListener('click', function() {
          modal.classList.remove('hidden');
          overlay.classList.remove('hidden');
          });

          closeBtn.addEventListener('click', function() {
          modal.classList.add('hidden');
          overlay.classList.add('hidden');
          });

          overlay.addEventListener('click', function() {
          modal.classList.add('hidden');
          overlay.classList.add('hidden');
          });

          // Optional: Close modal on ESC key
          document.addEventListener('keydown', function(e) {
          if (e.key === 'Escape') {
          modal.classList.add('hidden');
          overlay.classList.add('hidden');
          }
          });
        </script>

          <!-- Filter Form -->
          <form method="GET" class="mb-6 flex justify-end gap-2 font-mono">
            <input
              type="text"
              name="filter_name"
              value="<?= isset($_GET['filter_name']) ? htmlspecialchars($_GET['filter_name']) : '' ?>"
              placeholder="Filter by branch name"
              class="px-4 py-2 rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition duration-200"
              autocomplete="off"
            >
            <button
              type="submit"
              class="px-4 py-2 bg-cyan-400 hover:bg-cyan-500 text-white font-bold rounded-lg shadow transition"
            >Filter</button>
            <?php if (!empty($_GET['filter_name'])): ?>
              <a href="branches.php" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-cyan-700 font-bold rounded-lg shadow transition">Clear</a>
            <?php endif; ?>
          </form>
          <?php
            // Filtering logic
            $filterName = $_GET['filter_name'] ?? '';
            $params = [];
            $where = '';
            if ($filterName !== '') {
                $where = 'WHERE name LIKE :filter_name';
                $params[':filter_name'] = '%' . $filterName . '%';
            }

            // Fetch paginated branches with filter
            $stmt = $pdo->prepare("SELECT * FROM branches $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val, PDO::PARAM_STR);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $branches = $stmt->fetchAll();

            // Count total for pagination with filter
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM branches $where");
            foreach ($params as $key => $val) {
                $countStmt->bindValue($key, $val, PDO::PARAM_STR);
            }
            $countStmt->execute();
            $total = $countStmt->fetchColumn();
            $totalPages = ceil($total / $limit);
          ?>

        <!-- Export to PDF Button -->
        <div class="mb-6 flex justify-end">
        <button id="export-pdf-btn"
        class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-500 to-green-400 hover:from-green-400 hover:to-cyan-500 text-white font-bold rounded-lg shadow-lg transition duration-300 font-mono tracking-widest">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Export to PDF
        </button>
        </div>

        <?php
        // Fetch all branches for PDF export
        $allBranchesStmt = $pdo->query("SELECT * FROM branches ORDER BY created_at DESC");
        $allBranches = $allBranchesStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <!-- jsPDF CDN -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script>
          document.getElementById('export-pdf-btn').addEventListener('click', function () {
          // Get all branches data from PHP
          const branches = <?php echo json_encode($allBranches); ?>;

          // Prepare table columns and rows
          const columns = [
            { header: "#", dataKey: "id" },
            { header: "Name", dataKey: "name" },
            { header: "Phone", dataKey: "phone" },
            { header: "E-mail", dataKey: "email" },
            { header: "Location", dataKey: "location" },
            { header: "Active", dataKey: "is_active" },
            { header: "Created At", dataKey: "created_at" }
          ];

          // Format rows
          const rows = branches.map(branch => ({
            id: branch.id,
            name: branch.name,
            phone: branch.phone,
            email: branch.email,
            location: branch.location,
            is_active: branch.is_active == 1 ? "Active" : "Inactive",
            created_at: branch.created_at
          }));

          // Create jsPDF instance
          const { jsPDF } = window.jspdf;
          const doc = new jsPDF({
            orientation: 'landscape',
            unit: 'pt',
            format: 'A4'
          });

          // Title
          doc.setFontSize(18);
          doc.text("Branch List", 40, 40);

          // AutoTable
          if (doc.autoTable) {
            doc.autoTable({
            head: [columns.map(col => col.header)],
            body: rows.map(row => columns.map(col => row[col.dataKey])),
            startY: 60,
            styles: { fontSize: 10 },
            headStyles: { fillColor: [8, 145, 178] }
            });
          } else if (window.jspdf && window.jspdf.autoTable) {
            window.jspdf.autoTable(doc, {
            head: [columns.map(col => col.header)],
            body: rows.map(row => columns.map(col => row[col.dataKey])),
            startY: 60,
            styles: { fontSize: 10 },
            headStyles: { fillColor: [8, 145, 178] }
            });
          } else {
            alert('jsPDF autoTable plugin is required for table export.');
            return;
          }

          doc.save("branches.pdf");
          });
        </script>
        <!-- jsPDF AutoTable CDN -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
      </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border border-cyan-100 rounded-xl shadow font-mono">
        <thead class="bg-cyan-50">
          <tr>
            <th class="p-3">#</th>
            <th class="p-3">Name</th>
            <th class="p-3">Phone</th>
            <th class="p-3">E-mail</th>
            <th class="p-3">Location</th>
            <th class="p-3">Active</th>
            <th class="p-3">Created At</th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($branches as $branch): ?>
            <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition">
              <td class="p-3"><?= htmlspecialchars($branch['id']) ?></td>
              <td class="p-3"><?= htmlspecialchars($branch['name']) ?></td>
              <td class="p-3"><?= htmlspecialchars($branch['phone']) ?></td>
              <td class="p-3"><?= htmlspecialchars($branch['email']) ?></td>
              <td class="p-3"><?= htmlspecialchars($branch['location']) ?></td>
              <?php
              if ($branch['is_active'] == 1) {
                $active = true; 
                $activeStyle= 'text-green-500 font-bold rounded-full';
              } else {
                $active = false;
                $activeStyle= 'text-red-500 rounded-full';
              }
              ?>
              <td class="<?= $activeStyle ?>">
                <span><?= htmlspecialchars($active ? 'Active' : 'Inactive') ?></span>
              </td>
              <td class="p-3"><?= htmlspecialchars($branch['created_at']) ?></td>
              <td class="p-3">
                <div class="flex gap-2">
                  <a href="edit_branch.php?id=<?= $branch['id'] ?>"
                    class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-lg font-semibold shadow transition">Edit</a>
                  <form action="handlers/delete_branch.php" method="POST" class="inline-block">
                    <input type="hidden" name="id" value="<?= $branch['id'] ?>">
                    <button type="submit"
                      class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg font-semibold shadow transition"
                      onclick="return confirm('Are you sure?')">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-center gap-2 font-mono">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>"
          class="px-4 py-2 rounded-lg border border-cyan-200 <?= $i == $page ? 'bg-cyan-400 text-white font-bold shadow' : 'bg-white text-cyan-700 hover:bg-cyan-50' ?> transition">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
  </div>
</body>
</html>
