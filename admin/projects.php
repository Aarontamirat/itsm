<?php
require '../config/db.php';
session_start();

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Fetch all projects
$stmt = $pdo->query("SELECT p.*, u.name as assigned_name FROM projects p 
LEFT JOIN users u ON p.assigned_to = u.id ORDER BY p.created_at DESC");
$projects = $stmt->fetchAll();

// filter projects based on title, status, staff, and date
$title = isset($_GET['title']) ? trim($_GET['title']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$staff = isset($_GET['staff']) ? trim($_GET['staff']) : '';
$fromdate = isset($_GET['fromdate']) ? trim($_GET['fromdate']) : '';
$todate = isset($_GET['todate']) ? trim($_GET['todate']) : '';
$where = [];
if ($title) {
    $where[] = "p.title LIKE :title";
}
if ($status) {
    $where[] = "p.status = :status";
}
if ($staff) {
    $where[] = "p.assigned_to = :staff";
}
if ($fromdate) {
    $where[] = "DATE(p.created_at) >= DATE(:fromdate)";
}
if ($todate) {
    $where[] = "DATE(p.created_at) <= DATE(:todate)";
}
// Build WHERE clause as you did
$whereSQL = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Pagination variables
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Sanitize numeric inputs
$offset = max(0, (int)$offset);
$perPage = max(1, (int)$perPage);

// Prepare final SQL with literal LIMIT values
$sql = "SELECT p.*, u.name as assigned_name FROM projects p 
LEFT JOIN users u ON p.assigned_to = u.id $whereSQL
ORDER BY p.created_at DESC LIMIT $offset, $perPage";

$stmt = $pdo->prepare($sql);

// Bind filters
if ($title) $stmt->bindValue(':title', '%' . $title . '%', PDO::PARAM_STR);
if ($status) $stmt->bindValue(':status', $status, PDO::PARAM_STR);
if ($staff) $stmt->bindValue(':staff', $staff, PDO::PARAM_INT);
if ($fromdate) $stmt->bindValue(':fromdate', $fromdate, PDO::PARAM_STR);
if ($todate) $stmt->bindValue(':todate', $todate, PDO::PARAM_STR);

$stmt->execute();
$projects = $stmt->fetchAll();

// Fetch all staff for assignment
$stmt = $pdo->query("SELECT id, name FROM users WHERE role = 'staff'");
$staffList = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>

<head>
  <title>Project Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<!-- header and sidebar -->
<?php include '../includes/sidebar.php'; ?>
<?php include '../header.php'; ?>

<div class="max-w-7xl ms-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 fade-in tech-border glow mt-8">
  <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Project Management</h2>
  <p class="text-center text-cyan-500 mb-1 font-mono">Manage IT Projects and Assignments</p>

  <!-- filter form -->
  <div class="bg-cyan-100 border border-cyan-100 rounded-xl p-4 my-2">
    <form method="GET" class="mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block mb-1 font-bold text-cyan-700 font-mono">Title</label>
          <input type="text" name="title" class="w-full border border-cyan-200 rounded-lg p-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono" value="<?= htmlspecialchars($title) ?>" placeholder="Enter project title">
        </div>
        <div>
          <label class="block mb-1 font-bold text-cyan-700 font-mono">Status</label>
          <select name="status" class="w-full border border-cyan-200 rounded-lg p-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono">
            <option value="">All</option>
            <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="assigned" <?= $status == 'assigned' ? 'selected' : '' ?>>Assigned</option>
            <option value="fixed" <?= $status == 'fixed' ? 'selected' : '' ?>>Fixed</option>
            <option value="confirmed fixed" <?= $status == 'confirmed fixed' ? 'selected' : '' ?>>Confirmed Fixed</option>
            <option value="needs redo" <?= $status == 'needs redo' ? 'selected' : '' ?>>Needs Redo</option>
          </select>
        </div>
        <div>
          <label class="block mb-1 font-bold text-cyan-700 font-mono">Assigned To</label>
          <select name="staff" class="w-full border border-cyan-200 rounded-lg p-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono">
            <option value="">All</option>
            <?php foreach ($staffList as $s): ?>
              <option value="<?= $s['id'] ?>" <?= $staff == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block mb-1 font-bold text-cyan-700 font-mono">From Date</label>
          <input type="date" name="fromdate" class="w-full border border-cyan-200 rounded-lg p-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono" value="<?= htmlspecialchars($fromdate) ?>">
        </div>
        <div>
          <label class="block mb-1 font-bold text-cyan-700 font-mono">To Date</label>
          <input type="date" name="todate" class="w-full border border-cyan-200 rounded-lg p-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono" value="<?= htmlspecialchars($todate) ?>">
        </div>
        <div class="flex items-end">
          <button type="submit" class="inline-block bg-cyan-500 hover:bg-cyan-700 text-white font-bold rounded-lg shadow-lg px-6 py-2 font-mono tracking-widest transition duration-300 w-full md:w-auto text-center mb-2 md:mb-0">Filter</button>
        </div>
      </div>
    </form>
  </div>

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

  <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-8">
    <button onclick="document.getElementById('addModal').classList.remove('hidden')"
      class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 transform hover:scale-105 transition duration-300 font-mono tracking-widest w-full md:w-auto text-center">
      + Add New Project
    </button>
    
  <a href="admin_project_review.php" class="inline-block bg-cyan-900 hover:bg-cyan-600 text-white font-bold rounded-lg shadow-lg px-6 py-2 font-mono tracking-widest transition duration-300 w-full md:w-auto text-center mb-2 md:mb-0">
    Project Review
  </a>
  </div>

  <?php
  $selectedProject = null;
  if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT p.*, u.name as assigned_name FROM projects p LEFT JOIN users u ON p.assigned_to = u.id WHERE p.id = ?");
    $stmt->execute([$_GET['id']]);
    $selectedProject = $stmt->fetch();
  }
  ?>
  <?php if ($selectedProject): ?>
    <div class="overflow-x-auto mb-6 rounded-xl shadow-inner">
      <table class="w-full border border-cyan-100 bg-white bg-opacity-90 font-mono text-cyan-900">
        <thead>
          <tr class="bg-cyan-50 text-cyan-700 text-left">
            <th class="p-3 font-bold">#</th>
            <th class="p-3 font-bold">Title</th>
            <th class="p-3 font-bold">Description</th>
            <th class="p-3 font-bold">Status</th>
            <th class="p-3 font-bold">Assigned To</th>
            <th class="p-3 font-bold">Created At</th>
            <th class="p-3 font-bold">Estimated Cost</th>
            <th class="p-3 font-bold">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition cursor-pointer" onclick="window.location.href='project_detail.php?id=<?= $selectedProject['id'] ?>'">
            <td class="p-3">1</td>
            <td class="p-3 font-semibold"><?= htmlspecialchars($selectedProject['title']) ?></td>
            <td class="p-3"><?= htmlspecialchars(strlen($selectedProject['description']) > 40 ? substr($selectedProject['description'], 0, 40) . '...' : $selectedProject['description']) ?></td>
            <td class="p-3">
              <span class="px-2 py-1 rounded text-xs font-medium <?= $selectedProject['status'] === 'fixed' ? 'bg-green-400 text-white' : ($selectedProject['status'] === 'needs redo' ? 'bg-red-400 text-white' : 'bg-yellow-300 text-cyan-900') ?>">
                <?= ucfirst($selectedProject['status']) ?>
              </span>
            </td>
            <td class="p-3"><?= $selectedProject['assigned_name'] ?? '<i>Unassigned</i>' ?></td>
            <td class="p-3"><?= date('Y-m-d', strtotime($selectedProject['created_at'])) ?></td>
            <td class="p-3 whitespace-nowrap"><?= htmlspecialchars($selectedProject['estimated_cost']) ?? '<i>-</i>' ?></td>
            <td class="p-3 flex flex-col md:flex-row gap-2 whitespace-nowrap">

              <?php if ($selectedProject['status'] !== 'confirmed fixed'): ?>
                <!-- Edit -->
                <button type="button" onclick="event.stopPropagation();openEditModal(<?= htmlspecialchars(json_encode($selectedProject)) ?>)" class="bg-yellow-400 hover:bg-yellow-500 text-white font-bold px-3 py-1 rounded-lg shadow transition">Edit</button>
                <!-- Assign -->
                <button type="button" onclick="event.stopPropagation();openAssignModal(<?= $selectedProject['id'] ?>)" class="bg-green-400 hover:bg-green-500 text-white font-bold px-3 py-1 rounded-lg shadow transition">Assign</button>
                <!-- Delete -->
                <form method="POST" action="project_delete.php" onsubmit="event.stopPropagation();return confirm('Delete this project?');" class="inline">
                  <input type="hidden" name="id" value="<?= $selectedProject['id'] ?>">
                  <button type="submit" class="bg-red-400 hover:bg-red-500 text-white font-bold px-3 py-1 rounded-lg shadow transition">Delete</button>
                </form>
                <a href="projects.php" onclick="event.stopPropagation();" class="bg-gray-200 hover:bg-gray-300 text-cyan-700 font-bold px-3 py-1 rounded-lg shadow transition ml-2">Clear</a>
              <?php endif; ?>

            </td>
          </tr>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <div class="overflow-x-auto rounded-xl shadow-inner">
    <table class="w-full border border-cyan-100 bg-white bg-opacity-90 font-mono text-cyan-900">
      <thead>
        <tr class="bg-cyan-50 text-cyan-700 text-left">
          <th class="p-3 font-bold">#</th>
          <th class="p-3 font-bold">Title</th>
          <th class="p-3 font-bold">Description</th>
          <th class="p-3 font-bold">Status</th>
          <th class="p-3 font-bold">Progress</th>
          <th class="p-3 font-bold">Assigned To</th>
          <th class="p-3 font-bold">Remark</th>
          <th class="p-3 font-bold">Created At</th>
          <th class="p-3 font-bold">Deadline</th>
          <th class="p-3 font-bold">Estimated Cost</th>
          <th class="p-3 font-bold">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($projects as $i => $p): ?>
          <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition cursor-pointer" onclick="window.location.href='project_detail.php?id=<?= $p['id'] ?>'">
            <td class="p-3"><?= $i + 1 ?></td>
            <td class="p-3 font-semibold"><?= htmlspecialchars($p['title']) ?></td>
            <td class="p-3"><?= htmlspecialchars(strlen($p['description']) > 40 ? substr($p['description'], 0, 40) . '...' : $p['description']) ?></td>
            <td class="p-3 whitespace-nowrap">
              <span class="px-2 py-1 rounded text-xs font-medium <?= ($p['status'] === 'fixed' ? 'text-green-400 font-bold' : $p['status'] === 'confirmed fixed') ? 'bg-green-400 text-white' : ($p['status'] === 'needs redo' || $p['status'] === 'pending' ? 'bg-red-400 text-white animate-pulse' : 'bg-yellow-300 text-cyan-900') ?>">
                <?= ($p['status'] === 'confirmed fixed') ? 'Confirmed' : ucfirst($p['status'])?>
              </span>
            </td>
            <td class="p-3 whitespace-nowrap">
              <?php if ($p['main_status'] == 'under_process'): ?>
                <span class="px-2 py-1 rounded text-xs font-medium bg-red-400 text-white animate-pulse">Under Process</span>
              <?php elseif ($p['main_status'] == 'completed'): ?>
                <span class="px-2 py-1 rounded text-xs font-medium bg-green-400 text-white">Completed</span>
              <?php else: ?>
                <span class="px-2 py-1 rounded text-xs font-medium bg-yellow-300 text-cyan-900">Needs Attention</span>
              <?php endif; ?>
            </td>
            <td class="p-3"><?= $p['assigned_name'] ?? '<i>Unassigned</i>' ?></td>
            <td class="p-3"><?= $p['remark'] ?? '<i>-</i>' ?></td>
            <td class="p-3 whitespace-nowrap"><?= date('Y-m-d', strtotime($p['created_at'])) ?></td>
            <td class="p-3 whitespace-nowrap">
              <?php
              if ($p['status'] !== 'confirmed fixed') {
                $deadline_date = $p['deadline_date'];
                if ($deadline_date) {
                  $diff = strtotime($deadline_date) - time();
                  $days = floor($diff / (60 * 60 * 24));
                  $warning = null;
                  if ($days < 0) {
                    $warning = 'bg-red-300 text-red-900 animate-pulse';
                  } elseif ($days < 3) {
                    $warning = 'bg-yellow-100 text-yellow-700';
                  } else {
                    $warning = 'bg-green-100 text-green-700';
                  }
                ?>
                <?php if ($diff < 0): ?>
                  <span class="px-2 py-1 rounded text-xs font-medium <?= $warning ?>"><?= htmlspecialchars($deadline_date) ?> (Expired)</span>
                <?php else: ?>
                  <span class="px-2 py-1 rounded text-xs font-medium <?= $warning ?>"><?= htmlspecialchars($deadline_date) ?> (<?= $days ?> days left)</span>
                <?php endif; ?>
                <?php } else { ?>
                <i>-</i>
                <?php } ?>
                <?php } else{ if($p['deadline_date']) echo '<span class="bg-green-100 text-green-700">' . htmlspecialchars( $p['deadline_date']) . '</span>'; } ?>
            </td>

            <td class="p-3 whitespace-nowrap"><?= htmlspecialchars($p['estimated_cost']) ?? '<i>-</i>' ?></td>

            <td class="p-3 whitespace-nowrap">
              <?php if ($p['status'] !== 'confirmed fixed'): ?>
                <!-- Edit -->
                <button type="button" onclick="event.stopPropagation();openEditModal(<?= htmlspecialchars(json_encode($p)) ?>)" class="bg-yellow-400 hover:bg-yellow-500 text-white font-bold px-3 py-1 rounded-lg shadow transition">Edit</button>
                <!-- Assign -->
                <button type="button" onclick="event.stopPropagation();openAssignModal(<?= $p['id'] ?>)" class="bg-green-400 hover:bg-green-500 text-white font-bold px-3 py-1 rounded-lg shadow transition">Assign</button>
                <!-- Delete -->
                <form method="POST" action="project_delete.php" onsubmit="event.stopPropagation();return confirm('Delete this project?');" class="inline">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <button type="submit" class="bg-red-400 hover:bg-red-500 text-white font-bold px-3 py-1 rounded-lg shadow transition">Delete</button>
                </form>
              <?php endif; ?>
            </td>

          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- pagination -->
  <div class="mt-6 flex justify-center">
    <nav class="flex items-center space-x-2">
      <?php
      $totalProjects = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
      $totalPages = ceil($totalProjects / $perPage);
      for ($i = 1; $i <= $totalPages; $i++):
        if ($i == $page): ?>
          <span class="px-3 py-1 bg-cyan-500 text-white font-bold rounded-lg"><?= $i ?></span>
        <?php else: ?>
          <a href="?page=<?= $i ?>&title=<?= urlencode($title) ?>&status=<?= urlencode($status) ?>&staff=<?= urlencode($staff) ?>&fromdate=<?= urlencode($fromdate) ?>&todate=<?= urlencode($todate) ?>" class="px-3 py-1 bg-cyan-200 hover:bg-cyan-300 text-cyan-800 font-bold rounded-lg"><?= $i ?></a>
        <?php endif;
      endfor; ?>
    </nav>
  </div>

<!-- Add Project Modal -->
<div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
  <div class="bg-white w-full max-w-lg p-6 rounded-2xl shadow-2xl relative border-2 border-cyan-200 text-cyan-800">
    <button onclick="document.getElementById('addModal').classList.add('hidden')" class="absolute top-2 right-4 text-gray-600 text-xl">×</button>
    <h3 class="text-2xl font-semibold mb-4 text-cyan-700 font-mono">Create New Project</h3>
    <form action="project_create.php" method="POST">
      <div class="mb-4">
        <label class="block mb-1 font-bold text-cyan-700 font-mono">Project Title</label>
        <input type="text" name="title" required class="w-full border border-cyan-200 rounded-lg px-3 py-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono">
      </div>
      <div class="mb-4">
        <label class="block mb-1 font-bold text-cyan-700 font-mono">Description</label>
        <textarea name="description" class="w-full border border-cyan-200 rounded-lg px-3 py-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono"></textarea>
      </div>
      <div class="mb-4">
        <label class="block mb-1 font-bold text-cyan-700 font-mono" for="deadline_date">Deadline Date</label>
        <input type="date" name="deadline_date" id="deadline_date" class="w-full border border-cyan-200 rounded-lg px-3 py-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono">
      </div>
      <div class="flex justify-end">
        <button type="submit" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 font-mono tracking-widest">Save Project</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
  <div class="bg-white w-full max-w-lg p-6 rounded-2xl shadow-2xl relative border-2 border-cyan-200 text-cyan-700">
    <button onclick="document.getElementById('editModal').classList.add('hidden')" class="absolute top-2 right-4 text-gray-600 text-xl">×</button>
    <h3 class="text-2xl font-semibold mb-4 text-cyan-700 font-mono">Edit Project</h3>
    <form action="project_edit.php" method="POST">
      <input type="hidden" name="id" id="editId">
      <div class="mb-4">
        <label class="block mb-1 font-bold text-cyan-700 font-mono">Project Title</label>
        <input type="text" name="title" id="editTitle" required class="w-full border border-cyan-200 rounded-lg px-3 py-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono">
      </div>
      <div class="mb-4">
        <label class="block mb-1 font-bold text-cyan-700 font-mono">Description</label>
        <textarea name="description" id="editDescription" class="w-full border border-cyan-200 rounded-lg px-3 py-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono"></textarea>
      </div>
      <div class="mb-4">
        <label class="block mb-1 font-bold text-cyan-700 font-mono">Deadline</label>
        <input type="date" name="deadline_date" id="editDeadline_date" class="w-full border border-cyan-200 rounded-lg px-3 py-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono">
      </div>
      <div class="flex justify-end">
        <button type="submit" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 font-mono tracking-widest">Update</button>
      </div>
    </form>
  </div>
</div>

<!-- Assign Project Modal -->
<div id="assignModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
  <div class="bg-white w-full max-w-md p-6 rounded-2xl shadow-2xl relative border-2 border-cyan-200">
    <button onclick="document.getElementById('assignModal').classList.add('hidden')" class="absolute top-2 right-4 text-gray-600 text-xl">×</button>
    <h3 class="text-2xl font-semibold mb-4 text-cyan-700 font-mono">Assign Project</h3>
    <form action="project_assign.php" method="POST">
      <input type="hidden" name="project_id" id="assignProjectId">
      <label class="block mb-1 font-medium text-cyan-700 font-mono">Select IT Staff</label>
      <select name="assigned_to" required class="w-full border border-cyan-200 rounded-lg px-3 py-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono">
        <option value="">-- Select Staff --</option>
        <?php
        $staff = $pdo->query("SELECT id, name FROM users WHERE role = 'staff'")->fetchAll();
        foreach ($staff as $s):
        ?>
          <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <div class="flex justify-end mt-4">
        <button type="submit" class="bg-gradient-to-r from-green-400 via-cyan-300 to-cyan-400 hover:from-cyan-400 hover:to-green-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 font-mono tracking-widest">Assign</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<script>
  function openEditModal(project) {
    document.getElementById('editId').value = project.id;
    document.getElementById('editTitle').value = project.title;
    document.getElementById('editDescription').value = project.description;
    document.getElementById('editDeadline_date').value = project.deadline_date;
    document.getElementById('editModal').classList.remove('hidden');
  }
</script>

<!-- Assign Modal -->
<script>
  function openAssignModal(projectId) {
    document.getElementById('assignProjectId').value = projectId;
    document.getElementById('assignModal').classList.remove('hidden');
  }
</script>

</body>
</html>
