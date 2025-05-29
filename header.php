<?php
include 'config/db.php'; // Include your database connection file

// fetch user data
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($user['profile_picture']) && !empty($user['profile_picture'])) {
    $user['profile_image'] = '../uploads/' . $user['profile_picture'];
} else {
    $user['profile_image'] = '../uploads/default_avatar.png'; // Default profile image
}


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
          baseUrl = 'my_incident_history.php?id=';
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
        }, 10000000000);  // Wait 10 seconds before clearing the UI
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




<!-- profile dropdown -->
  <!-- Profile Section in Header -->
<div class="relative inline-block text-left">
  <button id="profileDropdownBtn" class="flex items-center gap-2 focus:outline-none">
    <img src="<?= $user['profile_image'] ?>" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-gray-700 shadow">
    <span class="md:inline text-gray-700 text-sm"><?= htmlspecialchars($user['name'] ?? 'Profile') ?></span>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
  </button>

  <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-md z-50">
    <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">👤 My Profile</a>
    <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">🚪 Logout</a>
  </div>
</div>
</header>


<script>
  // Profile Dropdown Functionality
  // Toggle profile dropdown visibility
  const profileBtn = document.getElementById('profileDropdownBtn');
  const profileDropdown = document.getElementById('profileDropdown');

  profileBtn.addEventListener('click', () => {
    profileDropdown.classList.toggle('hidden');
  });

  document.addEventListener('click', (e) => {
    if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
      profileDropdown.classList.add('hidden');
    }
  });
</script>
