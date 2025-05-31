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
<aside id="sidebar" class="bg-gradient-to-b from-blue-600 via-blue-500 to-blue-400 dark:from-gray-900 dark:via-gray-800 dark:to-gray-700 shadow-xl h-screen fixed transition-all duration-500 ease-in-out w-64 overflow-hidden z-10 flex flex-col">
  <div class="flex items-center justify-between p-4">
    <h2 class="text-2xl font-extrabold text-white tracking-wide transition-all duration-500" id="sidebar-title">IT Support</h2>
    <button id="sidebarToggle" class="text-white hover:text-yellow-300 focus:outline-none transition-transform duration-500">
      <svg id="sidebarToggleIcon" class="w-7 h-7 transition-transform duration-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>
  </div>
  <nav class="space-y-2 px-4 flex-1">
    <!-- For Admin -->
    <?php if ($_SESSION['role'] === 'admin'){ ?>
    <a href="../admin/admin_dashboard.php" class="group flex items-center space-x-3 text-white hover:bg-blue-700 rounded-lg px-3 py-2 transition-all duration-300">
      <span class="text-xl transition-transform duration-300 group-hover:scale-110">🏠</span>
      <span class="nav-text transition-all duration-300">Dashboard</span>
    </a>
    <a href="../admin/users.php" class="group flex items-center space-x-3 text-white hover:bg-blue-700 rounded-lg px-3 py-2 transition-all duration-300">
      <span class="text-xl transition-transform duration-300 group-hover:scale-110">👥</span>
      <span class="nav-text transition-all duration-300">Users</span>
    </a>
    <a href="../admin/branches.php" class="group flex items-center space-x-3 text-white hover:bg-blue-700 rounded-lg px-3 py-2 transition-all duration-300">
      <span class="text-xl transition-transform duration-300 group-hover:scale-110">🏢</span>
      <span class="nav-text transition-all duration-300">Branches</span>
    </a>
    <a href="../admin/categories.php" class="group flex items-center space-x-3 text-white hover:bg-blue-700 rounded-lg px-3 py-2 transition-all duration-300">
      <span class="text-xl transition-transform duration-300 group-hover:scale-110">📂</span>
      <span class="nav-text transition-all duration-300">Categories</span>
    </a>
    <a href="../admin/incidents.php" class="group flex items-center space-x-3 text-white hover:bg-blue-700 rounded-lg px-3 py-2 transition-all duration-300">
      <span class="text-xl transition-transform duration-300 group-hover:scale-110">📝</span>
      <span class="nav-text transition-all duration-300">Incidents</span>
    </a>
    <a href="../admin/kb_list.php" class="group flex items-center space-x-3 text-white hover:bg-blue-700 rounded-lg px-3 py-2 transition-all duration-300">
      <span class="text-xl transition-transform duration-300 group-hover:scale-110">❓</span>
      <span class="nav-text transition-all duration-300">Knowledge Base</span>
    </a>
    <a href="../admin/reports.php" class="group flex items-center space-x-3 text-white hover:bg-blue-700 rounded-lg px-3 py-2 transition-all duration-300">
      <span class="text-xl transition-transform duration-300 group-hover:scale-110">📊</span>
      <span class="nav-text transition-all duration-300">Reports</span>
    </a>
    <a href="../admin/incident_history.php" class="group flex items-center space-x-3 text-white hover:bg-blue-700 rounded-lg px-3 py-2 transition-all duration-300">
      <span class="text-xl transition-transform duration-300 group-hover:scale-110">📁</span>
      <span class="nav-text transition-all duration-300">Audit Logs</span>
    </a>
    <!-- For IT Staff -->
    <?php }elseif($_SESSION['role'] === 'staff'){ ?>
    <a href="../it_staff/it_staff_dashboard.php" class="group flex items-center space-x-3 text-white hover:bg-blue-700 rounded-lg px-3 py-2 transition-all duration-300">
      <span class="text-xl transition-transform duration-300 group-hover:scale-110">🏠</span>
      <span class="nav-text transition-all duration-300">Dashboard</span>
    </a>
    <a href="../it_staff/my_incidents.php" class="group flex items-center space-x-3 text-white hover:bg-blue-700 rounded-lg px-3 py-2 transition-all duration-300">
      <span class="text-xl transition-transform duration-300 group-hover:scale-110">📝</span>
      <span class="nav-text transition-all duration-300">My Incidents</span>
    </a>
    <a href="../it_staff/kb_list.php" class="group flex items-center space-x-3 text-white hover:bg-blue-700 rounded-lg px-3 py-2 transition-all duration-300">
      <span class="text-xl transition-transform duration-300 group-hover:scale-110">❓</span>
      <span class="nav-text transition-all duration-300">Knowledge Base</span>
    </a>
    <a href="../it_staff/reports.php" class="group flex items-center space-x-3 text-white hover:bg-blue-700 rounded-lg px-3 py-2 transition-all duration-300">
      <span class="text-xl transition-transform duration-300 group-hover:scale-110">📊</span>
      <span class="nav-text transition-all duration-300">Reports</span>
    </a>
    <!-- For End User -->
    <?php }elseif ($_SESSION['role'] === 'user'){ ?>
    <a href="../user/user_dashboard.php" class="group flex items-center space-x-3 text-white hover:bg-blue-700 rounded-lg px-3 py-2 transition-all duration-300">
      <span class="text-xl transition-transform duration-300 group-hover:scale-110">🏠</span>
      <span class="nav-text transition-all duration-300">Dashboard</span>
    </a>
    <a href="../user/report_incident.php" class="group flex items-center space-x-3 text-white hover:bg-blue-700 rounded-lg px-3 py-2 transition-all duration-300">
      <span class="text-xl transition-transform duration-300 group-hover:scale-110">📝</span>
      <span class="nav-text transition-all duration-300">Report Incident</span>
    </a>
    <a href="../user/my_incident_history.php" class="group flex items-center space-x-3 text-white hover:bg-blue-700 rounded-lg px-3 py-2 transition-all duration-300">
      <span class="text-xl transition-transform duration-300 group-hover:scale-110">📝</span>
      <span class="nav-text transition-all duration-300">My Incidents</span>
    </a>
    <a href="../user/kb_list.php" class="group flex items-center space-x-3 text-white hover:bg-blue-700 rounded-lg px-3 py-2 transition-all duration-300">
      <span class="text-xl transition-transform duration-300 group-hover:scale-110">❓</span>
      <span class="nav-text transition-all duration-300">Knowledge Base</span>
    </a>
    <!-- Routing Error -->
    <?php }else { ?>
    <p class="block bg-red-100 text-red-700 p-2 rounded text-center">ROUTING ERROR</p>
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

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('w-64');
    sidebar.classList.toggle('w-16');
    navTextEls.forEach(el => el.classList.toggle('opacity-0'));
    navTextEls.forEach(el => el.classList.toggle('w-0'));
    title.classList.toggle('opacity-0');
    title.classList.toggle('w-0');
    // Animate toggle icon (rotate)
    collapsed = !collapsed;
    toggleIcon.style.transform = collapsed ? 'rotate(90deg)' : 'rotate(0deg)';
  });
</script>
