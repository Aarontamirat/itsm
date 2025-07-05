<?php
require '../config/db.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$projectId = intval($_GET['id']);

// Fetch project
$sql = "SELECT p.*, u_creator.name AS creator, u_staff.name AS staff
        FROM projects p
        LEFT JOIN users u_creator ON p.created_by=u_creator.id
        LEFT JOIN users u_staff ON p.assigned_to=u_staff.id
        WHERE p.id=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$projectId]);
$project = $stmt->fetch();
if (!$project) { die("Project not found."); }

// Fetch timeline logs
$logs = [];
// initial creation
$logs[] = ['time'=>$project['created_at'], 'event'=>"Created by ".$project['creator'], 'status'=>$project['status']];
// status changes via a project_logs table? If not, just use updated_at
if ($project['updated_at'] && $project['status']!=='pending') {
  $logs[] = ['time'=>$project['updated_at'], 'event'=>"Status: ".$project['status'], 'status'=>$project['status']];
}
// Comments
$stmt = $pdo->prepare("SELECT pc.comment, pc.created_at, u.name FROM project_comments pc JOIN users u ON pc.user_id=u.id WHERE pc.project_id=? ORDER BY pc.created_at ASC");
$stmt->execute([$projectId]);
$comments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Project Details</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<?php include '../includes/sidebar.php'; ?>
<?php include '../header.php'; ?>

<div class="max-w-7xl ms-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 fade-in tech-border glow mt-8">
  <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Project Details</h2>

  <div class="flex flex-col md:flex-row justify-center items-center gap-4 mb-8">
      <a href="projects.php" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 transform hover:scale-105 transition duration-300 tracking-widest">
        &larr; Back to Projects
      </a>
  </div>

  <p class="text-center text-cyan-500 mb-6 font-mono"><?= htmlspecialchars($project['title']) ?></p>

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

  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
    <div>
      <h3 class="text-xl font-bold text-cyan-700 font-mono mb-2">Project Info</h3>
      <div class="space-y-2 font-mono">
        <div>
          <span class="font-semibold text-cyan-700">Description:</span>
          <span class="text-gray-700"><?= nl2br(htmlspecialchars($project['description'])) ?></span>
        </div>
        <div>
          <span class="font-semibold text-cyan-700">Status:</span>
          <span class="capitalize px-2 py-1 rounded-lg <?= ($project['status']=='confirmed fixed') || ($project['status']=='fixed') ? 'bg-green-400 text-white' : ($project['status']=='assigned' ? 'bg-yellow-300 text-gray-800' : 'bg-red-500 text-white animate-pulse') ?>">
            <?= ucfirst($project['status']) ?>
          </span>
        </div>
        <div>
          <span class="font-semibold text-cyan-700">Assigned To:</span>
          <span><?= $project['staff'] ? htmlspecialchars($project['staff']) : '<i>Unassigned</i>' ?></span>
        </div>
        <div>
          <span class="font-semibold text-cyan-700">Remark:</span>
          <span><?= $project['remark'] ? htmlspecialchars($project['remark']) : '<i>-</i>' ?></span>
        </div>
        <div>
          <span class="font-semibold text-cyan-700">Created By:</span>
          <span><?= htmlspecialchars($project['creator']) ?></span>
        </div>
        <div>
          <span class="font-semibold text-cyan-700">Created At:</span>
          <span><?= htmlspecialchars($project['created_at']) ?></span>
        </div>
        <div>
          <span class="font-semibold text-cyan-700">Last Updated:</span>
          <span><?= htmlspecialchars($project['updated_at']) ?></span>
        </div>
      </div>
    </div>
    <div>
      <h3 class="text-xl font-bold text-cyan-700 font-mono mb-2">📌 Timeline</h3>
      <ul class="border-l-2 border-cyan-400 ml-2 mb-4 font-mono">
        <?php foreach($logs as $l): ?>
        <li class="mt-2 ml-4">
          <time class="text-xs text-gray-500"><?= date('Y-m-d H:i', strtotime($l['time'])) ?></time>
          <p class="text-cyan-900"><?= htmlspecialchars($l['event']) ?></p>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <hr class="my-6 border-cyan-200">

  <!-- Comments section -->
  <h3 class="text-xl font-bold text-cyan-700 font-mono mb-2">💬 Comments</h3>
  <div class="space-y-4 mb-4">
    <?php foreach($comments as $c): ?>
      <div class="bg-cyan-50 p-3 rounded-lg border border-cyan-100 font-mono">
        <p class="text-sm mb-1">
          <strong class="text-cyan-700"><?= htmlspecialchars($c['name']) ?></strong>
          <time class="text-xs text-gray-500"><?= date('Y-m-d H:i', strtotime($c['created_at'])) ?></time>
        </p>
        <p><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
      </div>
    <?php endforeach; ?>
    <?php if (empty($comments)): ?>
      <div class="text-center text-cyan-400 font-mono">No comments yet.</div>
    <?php endif; ?>
  </div>

  <?php if (in_array($_SESSION['role'], ['staff','admin'])): ?>
  <form action="project_comment_post.php" method="POST" class="mt-6">
    <input type="hidden" name="project_id" value="<?= $projectId ?>">
    <textarea name="comment" rows="3" class="w-full border border-cyan-200 rounded-lg p-2 font-mono bg-cyan-50 focus:ring-2 focus:ring-cyan-300 focus:outline-none transition" placeholder="Write a comment..."></textarea>
    <button class="mt-2 bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 transform hover:scale-105 transition duration-300 font-mono tracking-widest">
      Send
    </button>
  </form>
  <?php endif; ?>
</div>
</body>
</html>
