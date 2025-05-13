<!-- header.php -->
<header id="header" class="bg-white shadow-md p-4 pl-72 flex justify-between items-center">
  <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>

  <!-- theme toggle -->
  <button id="darkToggle" class="ml-4 text-gray-600 dark:text-gray-300 hover:text-blue-600">
  🌓
</button>

<!-- admin dropdown -->
  <div class="relative inline-block">
    <button class="flex items-center space-x-2 focus:outline-none" id="dropdownButton">
      <span class="text-gray-800 font-medium"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span>
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>
    <div id="dropdownMenu" class="hidden absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded shadow-lg">
      <a href="profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">🔐 Profile</a>
      <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100">🚪 Logout</a>
    </div>
  </div>
</header>

<script>
  // Toggle dropdown menu
  document.getElementById('dropdownButton').addEventListener('click', () => {
    const menu = document.getElementById('dropdownMenu');
    menu.classList.toggle('hidden');
  });
</script>

<script>
  // Dark Mode Toggle
  const toggle = document.getElementById('darkToggle');
  toggle.addEventListener('click', () => {
    document.documentElement.classList.toggle('dark');
    localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
  });

  // Load preference on page load
  if (localStorage.theme === 'dark') {
    document.documentElement.classList.add('dark');
  }

</script>
