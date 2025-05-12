<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>IT Support Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    // Toggle dropdowns
    function toggleMenu(id) {
      const el = document.getElementById(id);
      el.classList.toggle('hidden');
    }
  </script>
</head>
<body class="bg-gray-100 text-white">
    <div class="container mx-auto p-6 bg-gray-800 rounded-lg shadow-lg mt-10">
        <h1 class="text-3xl font-bold mb-4">IT Support Dashboard</h1>
        <div class="flex justify items-center mb-4">
        <div class="relative inline-block text-left">
            <button onclick="toggleMenu('user-menu')" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded focus:outline-none">User Menu</button>
            <div id="user-menu" class="hidden absolute mt-2 w-48 bg-white text-gray-800 rounded-md shadow-lg z-20">
            <a href="#" class="block px-4 py-2 hover:bg-gray-200">Profile</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-200">Settings</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-200">Logout</a>
            </div>
        </div>
        <div class="relative inline-block text-left">
            <button onclick="toggleMenu('admin-menu')" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded focus:outline-none">Admin Menu</button>
            <div id="admin-menu" class="hidden absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-md shadow-lg z-20">
            <a href="#" class="block px-4 py-2 hover:bg-gray-200">Manage Users</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-200">Manage Incidents</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-200">Generate Reports</a>
            </div>
        </div>
        </div>
<script>
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('-translate-x-full');
  }
</script>