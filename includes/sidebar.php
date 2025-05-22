<!-- sidebar.php -->
<!-- <aside class="w-64 bg-white shadow-md p-4 fixed h-full transition-transform duration-300 ease-in-out">
  <h2 class="text-xl font-bold text-blue-600 mb-6">IT Support System</h2>
  <nav class="space-y-4"> -->
    <?php // if ($_SESSION['role'] === 'admin'): ?>
  <!-- Admin links -->
    <!-- <a href="dashboard.php" class="block text-gray-700 hover:text-blue-600 transition">🏠 Dashboard</a>
    <a href="users.php" class="block text-gray-700 hover:text-blue-600 transition">👥 Users</a>
    <a href="incidents.php" class="block text-gray-700 hover:text-blue-600 transition">📝 Incidents</a>
    <a href="faq.php" class="block text-gray-700 hover:text-blue-600 transition">❓ FAQs</a>
    <a href="reports.php" class="block text-gray-700 hover:text-blue-600 transition">📊 Reports</a>
    <a href="audit_logs.php" class="block text-gray-700 hover:text-blue-600 transition">📁 Audit Logs</a>
    <a href="logout.php" class="block text-red-600 hover:text-red-800 transition">🚪 Logout</a>

  </nav>
</aside> -->


<!-- sidebar.php -->
<aside id="sidebar" class="bg-white dark:bg-gray-800 shadow-md h-screen fixed transition-all duration-300 ease-in-out w-64 overflow-hidden z-10">
  <div class="flex items-center justify-between p-4">
    <h2 class="text-xl font-bold text-blue-600 dark:text-white" id="sidebar-title">IT Support</h2>
    <button id="sidebarToggle" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 focus:outline-none">
      ☰
    </button>
  </div>
  <nav class="space-y-4 px-4">


    <!-- For Admin -->
    <?php if ($_SESSION['role'] === 'admin'){ ?>
    <a href="../admin/admin_dashboard.php" class="flex items-center space-x-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 transition">
      <span>🏠</span><span class="nav-text">Dashboard</span>
    </a>
    <a href="../admin/users.php" class="flex items-center space-x-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 transition">
      <span>👥</span><span class="nav-text">Users</span>
    </a>
    <a href="../admin/branches.php" class="flex items-center space-x-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 transition">
      <span>👥</span><span class="nav-text">Branches</span>
    </a>
    <a href="../admin/incidents.php" class="flex items-center space-x-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 transition">
      <span>📝</span><span class="nav-text">Incidents</span>
    </a>
    <a href="../admin/faq_list.php" class="flex items-center space-x-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 transition">
      <span>❓</span><span class="nav-text">FAQs</span>
    </a>
    <a href="../admin/reports.php" class="flex items-center space-x-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 transition">
      <span>📊</span><span class="nav-text">Reports</span>
    </a>
    <a href="../admin/incident_history.php" class="flex items-center space-x-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 transition">
      <span>📁</span><span class="nav-text">Audit Logs</span>
    </a>


    <!-- For IT Staff -->
    <?php }elseif($_SESSION['role'] === 'staff'){ ?>
      <a href="../it_staff/it_staff_dashboard.php" class="block hover:bg-gray-700 p-2 text-gray-200 rounded">
      <span>🏠</span><span class="nav-text">Dashboard</span>
    </a>
    <a href="../it_staff/my_incidents.php" class="block hover:bg-gray-700 p-2 text-gray-200 rounded">
    <span>📝</span><span class="nav-text">Incidents</span>
    </a>
    <a href="../it_staff/faq_list.php" class="block hover:bg-gray-700 p-2 text-gray-200 rounded">
      <span>❓</span><span class="nav-text">FAQs</span>
    </a>
    <a href="../it_staff/reports.php" class="block hover:bg-gray-700 p-2 text-gray-200 rounded">
      <span>📊</span><span class="nav-text">Reports</span>
    </a>

    <!-- For End User -->
    <?php }elseif ($_SESSION['role'] === 'user'){ ?>
      <a href="/user-dashboard" class="block hover:bg-gray-700 p-2 rounded">Dashboard</a>

    <!-- Routing Error -->
     <?php }else { ?>
      <p class="block hover:bg-gray-700 p-2 text-red-700 rounded">ROUTING ERROR</p>
     <?php } ?>
  </nav>
</aside>

<script>
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const navTextEls = document.querySelectorAll('.nav-text');
  const title = document.getElementById('sidebar-title');

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('w-64');
    sidebar.classList.toggle('w-14');

    navTextEls.forEach(el => el.classList.toggle('hidden'));
    title.classList.toggle('hidden');
  });
</script>

