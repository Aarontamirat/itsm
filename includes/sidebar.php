<?php
// Set session timeout in seconds
$timeout_duration = 1800; // 1800 seconds = 30 minutes

// Check if last activity is set
if (isset($_SESSION['last_activity'])) {
  $elapsed_time = time() - $_SESSION['last_activity'];
  if ($elapsed_time > $timeout_duration) {
    // Session expired - destroy session and redirect via POST
    session_unset();
    session_destroy();
    echo '
    <form id="sessionExpiredForm" action="../login.php" method="post" style="display:none;">
      <input type="hidden" name="error" value="Session expired. Please log in again.">
    </form>
    <script>
      document.getElementById("sessionExpiredForm").submit();
    </script>
    ';
    exit();
  }
}
?>
<!-- sidebar.php -->
<aside id="sidebar" class="bg-white dark:bg-gray-900 bg-opacity-95 dark:bg-opacity-75 rounded-r-2xl shadow-2xl min-h-full fixed top-0 transition-all duration-500 ease-in-out w-64 overflow-hidden z-10 flex flex-col tech-border glow border-r-2 border-t-2 border-b-2 border-cyan-200 dark:border-cyan-800">
  <div class="flex items-center justify-between p-4 border-b border-cyan-100 dark:border-cyan-800">
    <h2 class="text-2xl font-extrabold text-cyan-700 dark:text-cyan-300 tracking-wide transition-all duration-500 font-mono" id="sidebar-title">IT Support</h2>
    <div class="flex items-center gap-2">
      <button id="sidebarToggle" class="text-cyan-700 dark:text-cyan-300 hover:text-green-400 dark:hover:text-green-300 focus:outline-none transition-transform duration-500">
        <svg id="sidebarToggleIcon" class="w-7 h-7 transition-transform duration-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>
  </div>
  <nav class="flex-1 overflow-y-auto overflow-x-hidden">
    <!-- For Admin -->
    <?php if ($_SESSION['role'] === 'admin'){ ?>

      <!-- dashboard -->
    <a href="../admin/admin_dashboard.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Dashboard">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">🏠</span>
      <span class="nav-text transition-all duration-300">Dashboard</span>
    </a>

    <!-- users -->
    <a href="../admin/users.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Users">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">👤</span>
      <span class="nav-text transition-all duration-300">Users</span>
    </a>

    <!-- branches -->
    <a href="../admin/branches.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Branches">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">🏢</span>
      <span class="nav-text transition-all duration-300">Branches</span>
    </a>

    <!-- assignments -->
    <a href="../admin/assign_staff_branches.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Assignments">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">🔗</span>
      <span class="nav-text transition-all duration-300">Assignments</span>
    </a>

    <!-- Categories -->
    <a href="../admin/categories.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Categories">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">🗂️</span>
      <span class="nav-text transition-all duration-300">Categories</span>
    </a>

    <!-- Incidents -->
    <a href="../admin/incidents.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Incidents">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">🚨</span>
      <span class="nav-text transition-all duration-300">Incidents</span>
    </a>

    <!-- Projects -->
    <a href="../admin/projects.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Projects">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">📁</span>
      <span class="nav-text transition-all duration-300">Projects</span>
    </a>

    <!-- Knowledge Base -->
    <a href="../admin/kb_list.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Knowledge Base">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">📚</span>
      <span class="nav-text transition-all duration-300">Knowledge Base</span>
    </a>

    <!-- Project Reports -->
    <a href="../admin/project_reports.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Project Reports">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">📊</span>
      <span class="nav-text transition-all duration-300">Project Reports</span>
    </a>

    <!-- Incident Reports -->
    <a href="../admin/reports.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Incident Report">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">📊</span>
      <span class="nav-text transition-all duration-300">Incident Reports</span>
    </a>

    <!-- Database -->
    <a href="../admin/backup.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Database">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">🗄️</span>
      <span class="nav-text transition-all duration-300">Database</span>
    </a>

    <!-- For IT Staff -->
    <?php }elseif($_SESSION['role'] === 'staff'){ ?>

    <!-- Dashboard -->
    <a href="../it_staff/it_staff_dashboard.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Dashboard">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">🏠</span>
      <span class="nav-text transition-all duration-300">Dashboard</span>
    </a>

    <!-- Incidents -->
    <a href="../it_staff/my_incidents.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Incidents">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">🚨</span>
      <span class="nav-text transition-all duration-300">Incidents</span>
    </a>

    <!-- Projects -->
    <a href="../it_staff/projects.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Projects">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">📁</span>
      <span class="nav-text transition-all duration-300">Projects</span>
    </a>

    <!-- Knowledge Base -->
    <a href="../it_staff/kb_list.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Knowledge Base">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">📚</span>
      <span class="nav-text transition-all duration-300">Knowledge Base</span>
    </a>

    <!-- Incident Reports -->
    <a href="../it_staff/staff_reports.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Incident Report">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">📊</span>
      <span class="nav-text transition-all duration-300">Incident Reports</span>
    </a>

    <!-- Project Reports -->
    <a href="../it_staff/project_reports.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Project Reports">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">📊</span>
      <span class="nav-text transition-all duration-300">Project Reports</span>
    </a>


    <!-- For End User -->
    <?php }elseif ($_SESSION['role'] === 'user'){ ?>

    <!-- Dashboard -->
    <a href="../user/user_dashboard.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Dashboard">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">🏠</span>
      <span class="nav-text transition-all duration-300">Dashboard</span>
    </a>

    <!-- Report Incident -->
    <a href="../user/report_incident.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Report Incident">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">🆘</span>
      <span class="nav-text transition-all duration-300">Request Support</span>
    </a>

    <!-- Incidents History -->
    <a href="../user/my_incident_history.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Incidents History">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">📜</span>
      <span class="nav-text transition-all duration-300">Incidents History</span>
    </a>

    <!-- Knowledge Base -->
    <a href="../user/kb_list.php" class="group flex items-center space-x-3 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-50 dark:hover:bg-gray-800 rounded-lg px-4 py-2 transition-all duration-300 font-mono font-semibold" title="Knowledge Base">
      <span class="text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110">📚</span>
      <span class="nav-text transition-all duration-300">Knowledge Base</span>
    </a>

    <!-- Routing Error -->
    <?php }else { ?>
    <p class="block bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 p-2 rounded text-center font-mono">ROUTING ERROR</p>
    <?php } ?>
  </nav>
</aside>

<script>
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const navTextEls = document.querySelectorAll('.nav-text');
  const title = document.getElementById('sidebar-title');
  const toggleIcon = document.getElementById('sidebarToggleIcon');

  let collapsed = false;

  function setSidebarState(isCollapsed) {
    if (isCollapsed) {
      sidebar.classList.add('w-16');
      sidebar.classList.remove('w-64');
      navTextEls.forEach(el => {
        el.classList.add('opacity-0', 'w-0');
      });
      title.classList.add('opacity-0', 'w-0');
      toggleIcon.style.transform = 'rotate(90deg)';
      collapsed = true;
    } else {
      sidebar.classList.add('w-64');
      sidebar.classList.remove('w-16');
      navTextEls.forEach(el => {
        el.classList.remove('opacity-0', 'w-0');
      });
      title.classList.remove('opacity-0', 'w-0');
      toggleIcon.style.transform = 'rotate(0deg)';
      collapsed = false;
    }
  }

  // Load saved state from localStorage
  const savedCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
  setSidebarState(savedCollapsed);

  toggleBtn.addEventListener('click', () => {
    collapsed = !collapsed;
    setSidebarState(collapsed);
    // Save state
    localStorage.setItem('sidebarCollapsed', collapsed);
  });
</script>

