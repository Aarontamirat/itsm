<?php
include 'config/db.php';

// Update last activity time stamp
$_SESSION['last_activity'] = time();


if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit();
}

if ($_SESSION['is_active'] == 0) {
  $_SESSION['error'] = "Your account is blocked. Please contact the system administrator.";
  header("Location: ../login.php");
  exit();
}


$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($user['profile_picture']) && !empty($user['profile_picture'])) {
  $user['profile_image'] = '../uploads/' . $user['profile_picture'];
} else {
  $user['profile_image'] = '../uploads/default_avatar.png';
}
?>

<style>
  /* Glassmorphism + Futuristic animated gradient background */
  .header-animated-bg {
    background: rgba(30, 41, 59, 0.82);
    background-image: linear-gradient(120deg, #0f172a 0%, #1e293b 40%, #06b6d4 70%, #818cf8 100%);
    background-blend-mode: overlay;
    background-size: 200% 200%;
    animation: gradientMove 8s linear infinite;
    border-bottom: 2px solid #06b6d4;
    box-shadow: 0 4px 24px 0 #0ea5e933, 0 1.5px 0 #818cf8;
    position: relative;
    overflow: visible;
    backdrop-filter: blur(16px) saturate(180%);
    -webkit-backdrop-filter: blur(16px) saturate(180%);
    min-height: 54px;
    height: 58px;
    padding-top: 0.25rem !important;
    padding-bottom: 0.25rem !important;
  }
  @keyframes gradientMove {
    0% {background-position: 0% 50%;}
    50% {background-position: 100% 50%;}
    100% {background-position: 0% 50%;}
  }
  /* Neon circuit lines */
  .header-animated-bg::before, .header-animated-bg::after {
    content: '';
    position: absolute;
    left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #06b6d4 40%, #818cf8 60%, transparent);
    opacity: 0.7;
    pointer-events: none;
    z-index: 1;
  }
  .header-animated-bg::before { top: 0; }
  .header-animated-bg::after { bottom: 0; }

  /* Dropdown animation */
  .dropdown-enter {
    opacity: 0;
    transform: translateY(-10px) scale(0.97);
    pointer-events: none;
    transition: all 0.22s cubic-bezier(.4,0,.2,1);
  }
  .dropdown-enter-active {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: auto;
    box-shadow: 0 8px 32px 0 #06b6d455, 0 1.5px 0 #818cf8;
  }
  /* Profile image with neon ring and glow */
  .profile-glow {
    box-shadow: 0 0 0 2px #06b6d4, 0 0 16px 2px #818cf855;
    transition: box-shadow 0.3s, border-color 0.3s;
    border: 2.5px solid #818cf8;
  }
  .profile-glow:hover {
    box-shadow: 0 0 0 5px #06b6d4, 0 0 32px 8px #818cf8cc;
    border-color: #06b6d4;
  }
  /* Notification bell pulse */
  .notif-pulse {
    animation: notifPulse 1.2s cubic-bezier(.4,0,.2,1) infinite;
  }
  @keyframes notifPulse {
    0% { filter: drop-shadow(0 0 0 #06b6d4); }
    70% { filter: drop-shadow(0 0 8px #06b6d4cc); }
    100% { filter: drop-shadow(0 0 0 #06b6d4); }
  }
  /* Techy glassmorphism for dropdowns */
  .glass {
    background: rgba(30, 41, 59, 0.85);
    backdrop-filter: blur(12px) saturate(180%);
    -webkit-backdrop-filter: blur(12px) saturate(180%);
    border: 1.5px solid #06b6d4;
    box-shadow: 0 8px 32px 0 #06b6d455;
  }
  /* Button styles */
  .tech-btn {
    background: linear-gradient(90deg, #06b6d4 0%, #818cf8 100%);
    color: #fff;
    font-weight: 600;
    border-radius: 0.75rem;
    box-shadow: 0 2px 8px #06b6d433;
    transition: background 0.3s, box-shadow 0.3s;
    padding-top: 0.25rem;
    padding-bottom: 0.25rem;
    min-height: 36px;
    height: 38px;
  }
  .tech-btn:hover {
    background: linear-gradient(90deg, #818cf8 0%, #06b6d4 100%);
    box-shadow: 0 4px 16px #818cf855;
  }
  /* Header title with clean professional font and subtle glow */
  .header-title {
    font-family: 'Segoe UI', Arial, sans-serif;
    font-size: 1.35rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    color: #f1f5f9;
    text-shadow: 0 1px 6px #06b6d455, 0 1px 0 #818cf822;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    animation: none;
    line-height: 1.1;
  }
  /* Custom scrollbar for dropdowns */
  #notifList::-webkit-scrollbar, #profileDropdown::-webkit-scrollbar {
    width: 6px;
    background: #1e293b;
  }
  #notifList::-webkit-scrollbar-thumb, #profileDropdown::-webkit-scrollbar-thumb {
    background: #06b6d4;
    border-radius: 3px;
  }
</style>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&display=swap" rel="stylesheet">

<header id="header" class="header-animated-bg shadow-md px-4 pl-72 flex justify-between items-center relative overflow-visible" style="min-height:54px;height:58px;">
  <h1 class="header-title">
    <svg width="28" height="28" fill="none" viewBox="0 0 32 32">
      <rect x="2" y="2" width="28" height="28" rx="6" fill="#06b6d4" opacity="0.15"/>
      <path d="M8 16h16M16 8v16" stroke="#06b6d4" stroke-width="2.5" stroke-linecap="round"/>
    </svg>
    ITSM Dashboard
  </h1>

  <!-- Notification Button -->
  <div class="relative inline-block text-left">
    <button id="notifBtn" class="relative flex items-center gap-2 px-4 py-1 text-sm tech-btn shadow-lg focus:outline-none transition-all duration-300" style="min-height:36px;height:38px;">
      <span id="notifBell" class="text-xl notif-pulse transition-transform duration-300">🔔</span>
      <span class="hidden md:inline">Notifications</span>
      <span id="notifCounter" class="absolute -top-2 -right-2 w-5 h-5 text-xs text-center text-white bg-pink-600 border-2 border-white rounded-full hidden shadow-lg"></span>
    </button>
    <div id="notifDropdown" class="dropdown-enter glass absolute right-0 z-20 w-80 mt-2 origin-top-right divide-y divide-cyan-200 rounded-xl shadow-2xl ring-1 ring-cyan-400 ring-opacity-30 hidden">
      <div class="py-1 max-h-60 overflow-y-auto" id="notifList">
        <p class="px-4 py-2 text-sm text-cyan-200">Loading notifications...</p>
      </div>
    </div>
  </div>

  <!-- Profile Section in Header -->
  <div class="relative inline-block text-left">
    <button id="profileDropdownBtn" class="flex items-center gap-2 focus:outline-none group">
      <img src="<?= $user['profile_image'] ?>" alt="Profile" class="w-10 h-10 rounded-full object-cover profile-glow transition-all duration-300">
      <span class="md:inline text-white text-base font-semibold group-hover:text-cyan-200 transition-colors"><?= htmlspecialchars($user['name'] ?? 'Profile') ?></span>
      <svg class="w-5 h-5 text-cyan-200 group-hover:text-cyan-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>
    <div id="profileDropdown" class="dropdown-enter glass hidden absolute right-0 mt-2 w-52 shadow-2xl rounded-xl z-50 overflow-hidden">
      <a href="profile.php" class="block px-5 py-3 text-base text-cyan-100 hover:bg-cyan-700/30 hover:text-cyan-300 transition-all duration-200">👤 My Profile</a>
      <a href="logout.php" class="block px-5 py-3 text-base text-rose-100 hover:bg-rose-700/30 hover:text-rose-300 transition-all duration-200">🚪 Logout</a>
    </div>
  </div>
</header>

<script>
const notifBtn = document.getElementById('notifBtn');
const notifCounter = document.getElementById('notifCounter');
const notifDropdown = document.getElementById('notifDropdown');
const notifList = document.getElementById('notifList');
const notifBell = document.getElementById('notifBell');

let notificationsCache = [];
let userRoles = '';

function loadNotifications() {
  fetch('../fetch_notifications.php')
    .then(res => res.json())
    .then(data => {
      notificationsCache = data.notifications;
      userRoles = data.user_role;
      updateNotificationUI(notificationsCache, userRoles);
    });
}

function updateNotificationUI(notifications, userRole) {
  let baseUrl = '';
  switch (userRole) {
    case 'admin': baseUrl = 'incidents.php?id='; break;
    case 'staff': baseUrl = 'my_incidents.php?id='; break;
    case 'user': baseUrl = 'my_incident_history.php?id='; break;
    default: baseUrl = '#';
  }

  notifList.innerHTML = '';
  if (notifications.length > 0) {
    notifCounter.textContent = notifications.length;
    notifCounter.classList.remove('hidden');
    notifications.forEach(n => {
      let hasFixed = n.message && n.message.includes('has been marked as fixed');
      // Determine URL based on related_project_id
      let url = (n.related_project_id !== null && n.related_project_id !== undefined)
        ? `projects.php?id=${n.related_project_id}`
        : `${baseUrl}${n.related_incident_id}`;
      notifList.innerHTML += `
      <a href="${url}" 
         class="block px-5 py-3 mb-2 last:mb-0 text-base font-medium text-slate-100 glass border-l-4 border-cyan-500 shadow-lg hover:bg-slate-900/80 hover:border-cyan-300 transition-all duration-200 rounded-xl group relative overflow-hidden backdrop-blur-md"
         style="border-radius: 0.85rem; border-left: 3px solid #38bdf8; box-shadow: 0 2px 10px 0 #38bdf844; position: relative; overflow: hidden; padding-left: 1.2rem; min-height: 44px;"
        >
        <span class="absolute left-0 top-0 h-full w-1 bg-gradient-to-b from-cyan-400 via-blue-500 to-indigo-600 opacity-70 rounded-l"></span>
        <span class="block text-cyan-100 text-sm font-mono py-3 pr-3" style="letter-spacing: 0.01em;">
          <svg class="inline-block mr-1 -mt-0.5" width="14" height="14" fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="7" stroke="#38bdf8" stroke-width="1.3"/><path d="M8 4v4l2 2" stroke="#818cf8" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
          ${n.message}
        </span>
        <span class="absolute left-3 bottom-2 text-xs text-cyan-300 font-mono tracking-tight opacity-70" style="letter-spacing:0.01em;">
          <svg class="inline-block mr-1 -mt-0.5" width="11" height="11" fill="none" viewBox="0 0 12 12"><rect x="1.5" y="1.5" width="9" height="9" rx="2" stroke="#38bdf8" stroke-width="1"/><path d="M6 3v3l1.5 1.5" stroke="#818cf8" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/></svg>
          ${n.created_at ? new Date(n.created_at).toLocaleString() : ''}
        </span>
        ${hasFixed ? `
          <div class="flex gap-2 my-3 ml-2">
            <button class="confirm-btn tech-btn px-3 py-1 text-xs" data-id="${n.related_incident_id}">Confirm</button>
            <button class="reopen-btn tech-btn px-3 py-1 text-xs bg-pink-600 hover:bg-pink-700" style="background:linear-gradient(90deg,#f43f5e 0%,#818cf8 100%);" data-id="${n.related_incident_id}">Reopen</button>
          </div>
        ` : ''}
      </a>
      `;
    });

    // Attach event listeners after rendering
    notifList.addEventListener('click', function(e) {
      // Confirm button
      if (e.target.classList.contains('confirm-btn')) {
      e.preventDefault();
      e.stopPropagation();
      const btn = e.target;
      const id = btn.getAttribute('data-id');
      fetch('../confirm_fixed.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({incident_id: id})
      }).then(res => res.json()).then(resp => {
        if (resp.success) {
        btn.textContent = 'Confirmed';
        btn.disabled = true;
        btn.classList.add('opacity-60');
        }
      });
      }
      // Reopen button
      if (e.target.classList.contains('reopen-btn')) {
      e.preventDefault();
      e.stopPropagation();
      const btn = e.target;
      const id = btn.getAttribute('data-id');
      fetch('../reopen_incident.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({incident_id: id})
      }).then(res => res.json()).then(resp => {
        if (resp.success) {
        btn.textContent = 'Reopened';
        btn.disabled = true;
        btn.classList.add('opacity-60');
        } else {
        btn.textContent = 'Error';
        }
      });
      }
    });

  } else {
    notifCounter.classList.add('hidden');
    notifList.innerHTML = '<p class="px-4 py-2 text-base text-cyan-200">No new notifications</p>';
  }
}

// Dropdown animation helpers
function showDropdown(dropdown) {
  dropdown.classList.remove('hidden');
  setTimeout(() => dropdown.classList.add('dropdown-enter-active'), 10);
}
function hideDropdown(dropdown) {
  dropdown.classList.remove('dropdown-enter-active');
  setTimeout(() => dropdown.classList.add('hidden'), 200);
}

notifBtn.addEventListener('click', (e) => {
  e.stopPropagation();
  if (notifDropdown.classList.contains('hidden')) {
    showDropdown(notifDropdown);
    fetch('../mark_notifications_seen.php', { method: 'POST' }).then(() => {
      setTimeout(() => {
        notificationsCache = [];
        updateNotificationUI([]);
      }, 10000);
      notifCounter.classList.add('hidden');
    });
  } else {
    hideDropdown(notifDropdown);
  }
});

document.addEventListener('click', (e) => {
  if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
    hideDropdown(notifDropdown);
  }
});

// Profile Dropdown
const profileBtn = document.getElementById('profileDropdownBtn');
const profileDropdown = document.getElementById('profileDropdown');

profileBtn.addEventListener('click', (e) => {
  e.stopPropagation();
  if (profileDropdown.classList.contains('hidden')) {
    showDropdown(profileDropdown);
  } else {
    hideDropdown(profileDropdown);
  }
});
document.addEventListener('click', (e) => {
  if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
    hideDropdown(profileDropdown);
  }
});

loadNotifications();
setInterval(loadNotifications, 30000);
</script>
