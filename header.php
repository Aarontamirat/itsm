<?php
include 'config/db.php'; // Include your database connection file



?>


<!-- header.php -->
<header id="header" class="bg-white shadow-md p-4 pl-72 flex justify-between items-center">
  <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>


  <!-- Notification Button -->
<div class="relative inline-block text-left">
  <button id="notifBtn" class="relative flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none">
    🔔 Notifications
    <span id="notifCounter" class="absolute -top-2 -right-2 w-5 h-5 text-xs text-center text-white bg-red-500 rounded-full hidden"></span>
  </button>

  <div id="notifDropdown" class="absolute right-0 z-20 w-80 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 hidden">
    <div class="py-1 max-h-60 overflow-y-auto" id="notifList">
      <p class="px-4 py-2 text-sm text-gray-500">Loading notifications...</p>
    </div>
  </div>
</div>

<script>
// Load notifications via AJAX
const notifBtn = document.getElementById('notifBtn');
const notifCounter = document.getElementById('notifCounter');
const notifDropdown = document.getElementById('notifDropdown');
const notifList = document.getElementById('notifList');

let notificationsCache = [];  // Cache the last fetched notifications

function loadNotifications() {
  fetch('../fetch_notifications.php')
    .then(res => res.json())
    .then(data => {
      notificationsCache = data.notifications; // Cache the notifications
      userRoles = data.user_role; // Store user role for later use
      updateNotificationUI(notificationsCache, userRoles);
    });
}

function updateNotificationUI(notifications, userRole) {

  let baseUrl = '';
      switch (userRole) {
        case 'admin':
          baseUrl = 'incidents.php?id=';
          break;
        case 'staff':
          baseUrl = 'my_incidents.php?id=';
          break;
        case 'user':
          baseUrl = 'incidents.php?id=';
          break;
        default:
          baseUrl = '#';
      }


  notifList.innerHTML = '';
  if (notifications.length > 0) {
    notifCounter.textContent = notifications.length;
    notifCounter.classList.remove('hidden');

    notifications.forEach(n => {
      notifList.innerHTML += `<a href="${baseUrl}${n.related_incident_id}" class="block px-4 py-2 text-sm text-gray-700 bg-orange-100 hover:bg-orange-300">${n.message}</a>`;
    });
  } else {
    notifCounter.classList.add('hidden');
    notifList.innerHTML = '<p class="px-4 py-2 text-sm text-gray-500">No new notifications</p>';
  }
}

notifBtn.addEventListener('click', () => {
  notifDropdown.classList.toggle('hidden');

  if (!notifDropdown.classList.contains('hidden')) {
    // Mark notifications as seen
    fetch('../mark_notifications_seen.php', { method: 'POST' })
      .then(() => {
        // Keep displaying the old data for a few seconds
        setTimeout(() => {
          notificationsCache = [];
          updateNotificationUI([]);
        }, 10000);  // Wait 10 seconds before clearing the UI
        notifCounter.classList.add('hidden');
      });
  }
});

document.addEventListener('click', (e) => {
  if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
    notifDropdown.classList.add('hidden');
  }
});

loadNotifications();
setInterval(loadNotifications, 30000);  // Reload every 30s

</script>




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

<!-- Notifications Dropdown script -->



 <?php
if ($_SESSION['role'] == 'admin') { ?>
<!-- <script>
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
              item.href = 'assign_incidents.php'; // Link to assigning incidents page
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
</script> -->
<?php } elseif ($_SESSION['role'] == 'staff') { ?>
  
  <!-- <script>
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
              item.href = 'my_incidents.php'; // Link to my incidents page
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
</script> -->

<?php }  ?>
