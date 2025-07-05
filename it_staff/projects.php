<?php
require '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT p.*, u.name AS assigned_by FROM projects p 
    LEFT JOIN users u ON p.assigned_to = u.id 
    WHERE p.assigned_to = ? ORDER BY p.created_at DESC");
$stmt->execute([$user_id]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filter by title, status, created_at{from, to}
$where = [];
$params = [];

// title filter
$title = isset($_GET['title']) ? $_GET['title'] : '';
if (!empty($title)) {
    $where[] = "title LIKE :title";
    $params['title'] = '%' . $title . '%';
}

// Status filter
$status = isset($_GET['status']) ? $_GET['status'] : '';
if (!empty($status)) {
    $where[] = "status = :status";
    $params['status'] = $status;
}

// Date range filter
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
if (!empty($from_date)) {
    $where[] = "DATE(created_at) >= :from_date";
    $params['from_date'] = $from_date;
}
if (!empty($to_date)) {
    $where[] = "DATE(created_at) <= :to_date";
    $params['to_date'] = $to_date;
}

$whereSQL = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// ----- Pagination -----
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total records
$countSQL = "SELECT COUNT(*) FROM projects $whereSQL";
$stmt = $pdo->prepare($countSQL);
$stmt->execute($params);
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Fetch paginated records
$sql = "SELECT * FROM projects $whereSQL ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);

// Bind filters first
foreach ($params as $k => $v) {
    $stmt->bindValue(':' . $k, $v);
}
// Then bind limit & offset
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Projects</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<!-- header and sidebar -->
<?php include '../includes/sidebar.php'; ?>
<?php include '../header.php'; ?>

<div class="max-w-7xl ms-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 fade-in tech-border glow mt-8">
  <h1 class="text-3xl font-extrabold text-center text-cyan-700 mb-6 tracking-tight font-mono">My Assigned Projects</h1>

  <!-- Filter form -->
    <form class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6" method="GET" action="projects.php">

      <!-- title filter -->
      <div>
        <label class="block mb-1 font-bold text-cyan-700 font-mono">Title</label>
        <input type="text" name="title" class="w-full border border-cyan-200 rounded-lg p-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono" value="<?= htmlspecialchars($title) ?>" placeholder="Enter project title">
      </div>

      <!-- status filter -->
      <div>
        <label class="block mb-1 font-bold text-cyan-700 font-mono">Status</label>
        <select name="status" class="w-full border border-cyan-200 rounded-lg p-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono">
          <option value="">All</option>
          <option value="Pending" <?= $status=='Pending'?'selected':''; ?>>Pending</option>
          <option value="Assigned" <?= $status=='Assigned'?'selected':''; ?>>Assigned</option>
          <option value="Fixed" <?= $status=='Fixed'?'selected':''; ?>>Fixed</option>
          <option value="Confirmed Fixed" <?= $status=='Confirmed Fixed'?'selected':''; ?>>ConfirmedFixed</option>
          <option value="Needs Redo" <?= $status=='Needs Redo'?'selected':''; ?>>Needs Redo</option>
        </select>
      </div>

      <!-- date range -->
      <div>
        <label class="block mb-1 font-bold text-cyan-700 font-mono">From Date</label>
        <input type="date" name="from_date" value="<?= htmlspecialchars($from_date) ?>" class="w-full border border-cyan-200 rounded-lg p-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono">
      </div>
      <div>
        <label class="block mb-1 font-bold text-cyan-700 font-mono">To Date</label>
        <input type="date" name="to_date" value="<?= htmlspecialchars($to_date) ?>" class="w-full border border-cyan-200 rounded-lg p-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono">
      </div>
      <div class="flex items-end">
        <button type="submit" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 font-mono tracking-widest w-full">Filter</button>
      </div>
    </form>

  
  <!-- Success/Error Messages -->
  <?php if (isset($_SESSION['success'])): ?>
    <div id="success-message" class="mb-4 text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
      <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
    </div>
    <script>
      setTimeout(() => document.getElementById('success-message').style.opacity = '1', 10);
      setTimeout(() => document.getElementById('success-message').style.opacity = '0', 3010);
    </script>
  <?php endif; ?>

  <!-- selected project by GET[id] -->
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
            <th class="p-3 font-bold">Remark</th>
            <th class="p-3 font-bold">Deadline</th>
            <th class="p-3 font-bold">Created At</th>
            <th class="p-3 font-bold">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition cursor-pointer" onclick="window.location.href='project_detail.php?id=<?= $selectedProject['id'] ?>'">
            <td class="p-3">1</td>
            <td class="p-3 font-semibold"><?= htmlspecialchars($selectedProject['title']) ?></td>
            <td class="p-3"><?= htmlspecialchars($selectedProject['description']) ?></td>
            <td class="p-3 whitespace-nowrap">
              <span class="px-2 py-1 rounded text-xs font-medium 
                <?= $selectedProject['status'] === 'fixed' ? 'text-green-400 font-bold' : 
                   ($selectedProject['status'] === 'confirmed fixed' ? 'bg-green-400 text-white' : 
                   ($selectedProject['status'] === 'redo' ? 'bg-red-400 text-white' : 
                   ($selectedProject['status'] === 'need_support' ? 'bg-blue-400 text-white' : 
                   ($selectedProject['status'] === 'assigned' ? 'bg-purple-400 text-white' : 'bg-yellow-300 text-cyan-900')))) ?>">
                <?= ucfirst($selectedProject['status']) ?>
              </span>
            </td>
            <td class="p-3"><?= htmlspecialchars($selectedProject['remark']) ?: '<i>-</i>' ?></td>
            <td class="p-3 whitespace-nowrap">
              <?php
              $deadline_date = $selectedProject['deadline_date'];
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
              <span class="px-2 py-1 whitespace-nowrap rounded <?= $warning ?? '' ?>"><?= htmlspecialchars($deadline_date) ?></span>
              <?php } else { ?>
              <i>-</i>
              <?php } ?>
            </td>
            <td class="p-3 whitespace-nowrap"><?= date('Y-m-d', strtotime($selectedProject['created_at'])) ?></td>
            <td class="p-3" onclick="event.stopPropagation()">
              <?php if ($selectedProject['status'] !== 'confirmed fixed'): ?>
              <form action="project_status_update.php" method="POST" class="flex gap-2 items-center">
                <input type="hidden" name="project_id" value="<?= $selectedProject['id'] ?>">
                <select name="status" class="border border-cyan-200 rounded-lg px-3 py-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono text-sm" required>
                  <option value="">--Select--</option>
                  <option value="fixed">Fixed</option>
                  <option value="need_support">Need Support</option>
                </select>
                <button type="submit" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-4 py-2 text-sm font-mono tracking-widest transform hover:scale-105 transition duration-300">Update</button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <div class="overflow-x-auto rounded-xl shadow-inner">
    <table class="min-w-full border border-cyan-100 bg-white bg-opacity-90 font-mono text-cyan-900">
      <thead class="bg-cyan-50 text-cyan-700">
        <tr>
          <th class="p-3 font-bold">#</th>
          <th class="p-3 font-bold">Title</th>
          <th class="p-3 font-bold">Description</th>
          <th class="p-3 font-bold">Status</th>
          <th class="p-3 font-bold">Remark</th>
          <th class="p-3 font-bold">Deadline</th>
          <th class="p-3 font-bold">Created At</th>
          <th class="p-3 font-bold">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($projects as $i => $p): ?>
          <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition cursor-pointer" onclick="window.location.href='project_detail.php?id=<?= $p['id'] ?>'">
            <td class="p-3"><?= $i + 1 ?></td>
            <td class="p-3 font-semibold"><?= htmlspecialchars($p['title']) ?></td>
            <td class="p-3"><?= nl2br(htmlspecialchars($p['description'])) ?></td>
            <td class="p-3 whitespace-nowrap">
              <span class="px-2 py-1 rounded text-xs font-medium 
                <?= $p['status'] === 'fixed' ? 'text-green-400 font-bold' : 
                   ($p['status'] === 'confirmed fixed' ? 'bg-green-400 text-white' : 
                   ($p['status'] === 'redo' ? 'bg-red-400 text-white' : 
                   ($p['status'] === 'need_support' ? 'bg-blue-400 text-white' : 
                   ($p['status'] === 'assigned' ? 'bg-purple-400 text-white' : 'bg-yellow-300 text-cyan-900')))) ?>">
                <?= ucfirst($p['status']) ?>
              </span>
            </td>
            <td class="p-3"><?= htmlspecialchars($p['remark']) ?: '<i>-</i>' ?></td>
            <td class="p-3 whitespace-nowrap">
              <?php
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
              <span class="px-2 py-1 whitespace-nowrap rounded <?= $warning ?? '' ?>"><?= htmlspecialchars($deadline_date) ?></span>
              <?php } else { ?>
              <i>-</i>
              <?php } ?>
            </td>
            <td class="p-3 whitespace-nowrap"><?= date('Y-m-d', strtotime($p['created_at'])) ?></td>
            <td class="p-3" onclick="event.stopPropagation()">
              <?php if ($p['status'] !== 'confirmed fixed'): ?>
              <form action="project_status_update.php" method="POST" class="flex gap-2 items-center">
                <input type="hidden" name="project_id" value="<?= $p['id'] ?>">
                <select name="status" class="border border-cyan-200 rounded-lg px-3 py-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono text-sm" required>
                  <option value="">--Select--</option>
                  <option value="fixed">Fixed</option>
                  <option value="need_support">Need Support</option>
                </select>
                <button type="submit" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-4 py-2 text-sm font-mono tracking-widest transform hover:scale-105 transition duration-300">Update</button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination links -->
    <?php if ($total_pages > 1): ?>
      <div class="flex justify-center mt-6 space-x-2 font-mono">
        <?php for ($i=1; $i <= $total_pages; $i++): ?>
          <a href="?<?= http_build_query(array_merge($_GET, ['page'=>$i])) ?>" class="px-4 py-2 rounded-lg border <?= $i==$page ? 'bg-cyan-400 text-white' : 'bg-cyan-100 text-cyan-800 hover:bg-cyan-200' ?>">
            <?= $i ?>
          </a>
        <?php endfor; ?>
      </div>
    <?php endif; ?>

</div>

</body>
</html>

