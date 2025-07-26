<?php
require '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

$stmt = $pdo->prepare("SELECT p.*, u.name AS staff_name FROM projects p 
  LEFT JOIN users u ON p.assigned_to = u.id 
  WHERE p.status IN ('fixed')
  ORDER BY p.updated_at DESC");
$stmt->execute();
$projects = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Review Projects</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <?php include '../includes/sidebar.php'; ?>
  <?php include '../header.php'; ?>

  <div class="max-w-7xl ms-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 fade-in tech-border glow mt-8">
    <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Project Review</h2>
    <p class="text-center text-cyan-500 mb-6 font-mono">Review and confirm completed or redone projects</p>

    <div class="flex flex-col md:flex-row justify-center items-center gap-4 mb-8">
      <a href="projects.php" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 transform hover:scale-105 transition duration-300 tracking-widest">
        &larr; Back to Projects
      </a>
    </div>

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

    <div class="overflow-x-auto rounded-xl shadow-inner">
      <table class="w-full border border-cyan-100 bg-white bg-opacity-90 font-mono text-cyan-900">
        <thead>
          <tr class="bg-cyan-50 text-cyan-700 text-left">
            <th class="p-3 font-bold">#</th>
            <th class="p-3 font-bold">Title</th>
            <th class="p-3 font-bold">Staff</th>
            <th class="p-3 font-bold">Status</th>
            <th class="p-3 font-bold">Progress</th>
            <th class="p-3 font-bold">Deadline</th>
            <th class="p-3 font-bold">Estimated Cost</th>
            <th class="p-3 font-bold">Updated</th>
            <th class="p-3 font-bold">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($projects)): ?>
            <tr>
              <td colspan="6" class="p-6 text-center text-cyan-400 font-semibold">No project found</td>
            </tr>
          <?php else: ?>
            <?php foreach ($projects as $i => $p): ?>
              <tr class="border-t border-cyan-100 hover:bg-cyan-50 transition">
                <td class="p-3"><?= $i + 1 ?></td>
                <td class="p-3"><?= htmlspecialchars($p['title']) ?></td>
                <td class="p-3"><?= htmlspecialchars($p['staff_name']) ?></td>
                <td class="p-3 font-medium">
                  <span class="<?= $p['status'] === 'fixed' ? 'bg-green-400 text-white px-2 rounded-lg' : 'bg-red-400 text-white px-2 rounded-lg' ?>">
                    <?= ucfirst($p['status']) ?>
                  </span>
                </td>
                <td class="p-3 font-medium">
                  <span class="<?= $p['main_status'] === 'completed' ? 'bg-green-400 text-white px-2 rounded-lg' : 'bg-yellow-400 text-white px-2 rounded-lg' ?>">
                    <?= ucfirst($p['main_status']) ?>
                  </span>
                </td>
                <td class="p-3 whitespace-nowrap"><?= date('Y-m-d', strtotime($p['deadline_date'])) ?></td>
                <td class="p-3 whitespace-nowrap"><?= number_format($p['estimated_cost'], 2) ?></td>
                <td class="p-3 whitespace-nowrap"><?= date('Y-m-d H:i', strtotime($p['updated_at'])) ?></td>
                <td class="p-3 flex flex-col md:flex-row gap-2">
                  <form action="admin_project_action.php" method="POST" class="flex gap-2">
                    <input type="hidden" name="project_id" value="<?= $p['id'] ?>">
                    <button name="action" value="confirm" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-4 py-1 transform hover:scale-105 transition duration-300 font-mono tracking-widest text-sm">✅ Confirm</button>
                    <button type="button" id="redo-button-<?= $p['id'] ?>" onclick="openRemarkModal(<?= $p['id'] ?>)" class="bg-red-500 hover:bg-red-600 text-white font-bold rounded-lg shadow-lg px-4 py-1 transform hover:scale-105 transition duration-300 font-mono tracking-widest text-sm">🔁 Needs Redo</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- create a modal to submit along with the form only when the redo button is clicked -->
    <div id="redo-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
  <div class="bg-white w-full max-w-lg p-6 rounded-2xl shadow-2xl relative border-2 border-cyan-200 text-cyan-800">
    <button type="button" onclick="closeRedoModal()" class="absolute top-2 right-4 text-gray-600 text-xl">×</button>
    <h3 class="text-2xl font-semibold mb-4 text-cyan-700 font-mono">Redo Project</h3>
    <form action="admin_project_action.php" method="POST">
      <input type="hidden" name="project_id" id="redo-project-id">
      <div class="mb-4">
        <label for="reason" class="block mb-1 font-bold text-cyan-700 font-mono">Reason for Redo</label>
        <textarea name="reason" id="reason" rows="4" class="w-full border border-cyan-200 rounded-lg px-3 py-2 bg-cyan-50 focus:ring-2 focus:ring-cyan-300 font-mono" required></textarea>
      </div>
      <div class="flex justify-end gap-2">
        <button type="button" onclick="closeRedoModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-mono hover:bg-gray-300">Cancel</button>
        <button type="submit" name="action" value="redo" class="bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg px-6 py-2 font-mono tracking-widest">Redo</button>
      </div>
    </form>
  </div>
</div>

    
    <!-- show modal when the redo-button is clicked -->
    <script>
      const redoButtons = document.querySelectorAll('[id^="redo-button-"]');
      const redoModal = document.getElementById('redo-modal');
      const redoProjectId = document.getElementById('redo-project-id');

      redoButtons.forEach(button => {
        button.addEventListener('click', (event) => {
          const projectId = event.target.id.split('-')[2];
          redoProjectId.value = projectId;
          redoModal.classList.remove('hidden');
        });
      });

      function closeRedoModal() {
        redoModal.classList.add('hidden');
      }
    </script>

  </div>

</body>
</html>
