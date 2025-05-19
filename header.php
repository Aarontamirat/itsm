<?php
include 'config/db.php'; // Include your database connection file

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = 1 AND seen = FALSE");
$stmt->execute();
$notifications = $stmt->fetchAll();

if (count($notifications) > 0) {
    $NotificationCount = count($notifications);
} else {
    $NotificationCount = 0;
}

?>


<!-- header.php -->
<header id="header" class="bg-white shadow-md p-4 pl-72 flex justify-between items-center">
  <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>

<!-- Notification Dropdown UI -->
<div class="relative inline-block text-left z-30">
  <button id="notificationBtn" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none">
    🔔 Notifications
    <p id="notifCount" class="inline-block w-5 h-5 text-xs text-center text-white bg-red-500 rounded-full">
      <?php
      if ($NotificationCount > 0) {
          echo $NotificationCount;
      } else {
          echo '0';
      }
      ?>
    </p>
  </button>

  <div id="notificationDropdown" class="absolute right-0 w-80 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 hidden max-h-96 overflow-y-auto">
    <div class="py-1" id="notifList">
      <!-- Populated by JS -->
      <div class="px-4 py-2 text-sm text-gray-500">Loading...</div>
    </div>
  </div>
</div>




<!-- admin dropdown -->
  <div class="relative inline-block">
    <button class="flex items-center space-x-2 focus:outline-none" id="dropdownButton">
      <span class="text-gray-800 font-medium"><?= htmlspecialchars($_SESSION['name'] ?? 'Who are you?') ?></span>
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>
    <div id="dropdownMenu" class="hidden absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded shadow-lg">
        <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100">🚪 Logout</a>
    </div>
  </div>
</header>

<script>
  // Admin dropdown menu
  document.getElementById('dropdownButton').addEventListener('click', () => {
    const menu = document.getElementById('dropdownMenu');
    menu.classList.toggle('hidden');
  });
</script>

<!-- Notifications Dropdown -->
<script>
  const btn = document.getElementById('notificationBtn');
  const dropdown = document.getElementById('notificationDropdown');
  const notifList = document.getElementById('notifList');
  const notifCount = document.getElementById('notifCount');

  // Toggle dropdown and mark as seen
  btn.addEventListener('click', () => {
    dropdown.classList.toggle('hidden');

    if (!dropdown.classList.contains('hidden')) {
      fetch('../mark_notifications_seen.php', { method: 'POST' })
        .then(res => res.json())
        .then(data => {
          notifList.innerHTML = '';
          if (data.notifications.length === 0) {
            notifList.innerHTML = `<div class="px-4 py-2 text-sm text-gray-500">No new notifications</div>`;
          } 
          else {
            data.notifications.forEach(notif => {
              const item = document.createElement('a');
              item.href = '#';
              item.className = 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100';
              item.textContent = notif.message;
              notifList.appendChild(item);
            });
          }

        })
        .catch(err => {
          console.error('Error fetching notifications:', err);
          notifList.innerHTML = `<div class="px-4 py-2 text-sm text-red-500">Error loading notifications</div>`;
        });
    }
  });

  // Close dropdown on outside click
  document.addEventListener('click', (e) => {
    if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.classList.add('hidden');
    }
  });
</script>
