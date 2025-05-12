<?php
$notificationCount = 3;
?>

<div class="flex h-screen">

<!-- Toggle Button (Hamburger) -->
<div class="fixed top-4 left-4 z-50 lg:hidden">
  <button onclick="toggleSidebar()" class="bg-blue-900 p-2 rounded text-white hover:bg-blue-800">
    ☰
  </button>
</div>
    
  <!-- Sidebar -->
  <aside class="w-64 bg-blue-900 shadow-md flex flex-col">
    <div class="p-6 text-center text-xl font-bold border-b border-blue-800">
      IT Support
    </div>

    <nav class="flex-1 overflow-y-auto p-4 space-y-4">
      <a href="#" class="block hover:bg-blue-800 px-4 py-2 rounded">User Management</a>

      <!-- Incidents -->
      <div>
        <button onclick="toggleMenu('incidentsMenu')" class="w-full text-left hover:bg-blue-800 px-4 py-2 rounded">
          Incidents ⌄
        </button>
        <div id="incidentsMenu" class="pl-6 space-y-1 hidden">
          <a href="#" class="block hover:bg-blue-800 px-2 py-1 rounded">Incidents List</a>
          <a href="#" class="block hover:bg-blue-800 px-2 py-1 rounded">Assign Incidents</a>
        </div>
      </div>

      <!-- Reports -->
      <div>
        <button onclick="toggleMenu('reportsMenu')" class="w-full text-left hover:bg-blue-800 px-4 py-2 rounded">
          Reports ⌄
        </button>
        <div id="reportsMenu" class="pl-6 space-y-1 hidden">
          <a href="#" class="block hover:bg-blue-800 px-2 py-1 rounded">User Reports</a>
          <a href="#" class="block hover:bg-blue-800 px-2 py-1 rounded">Incidents Reports</a>
          <a href="#" class="block hover:bg-blue-800 px-2 py-1 rounded">Audit Reports</a>
        </div>
      </div>
    </nav>

    <!-- Bottom Section -->
    <div class="p-4 border-t border-blue-800">
      <div class="flex items-center justify-between">
        <!-- Notification Badge -->
        <div class="relative">
          <button class="relative p-2 bg-blue-800 rounded-full hover:bg-blue-700">
            🔔
            <?php if ($notificationCount > 0): ?>
              <span class="absolute -top-1 -right-1 bg-red-600 text-xs font-bold px-1.5 py-0.5 rounded-full">
                <?= $notificationCount ?>
              </span>
            <?php endif; ?>
          </button>
        </div>

        <!-- Logout -->
        <form method="post" action="../../logout.php">
          <button class="bg-red-600 hover:bg-red-700 text-sm px-4 py-2 rounded font-semibold">
            Logout
          </button>
        </form>
      </div>
    </div>
  </aside>
