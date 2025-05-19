<?php
session_start();
require_once '../config/db.php';

$directoryName = basename(__DIR__);

?>
<!-- admin_dashboard.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100 min-h-screen">

  <?php include '../includes/sidebar.php'; ?>
  <div class="flex-1 ml-20">
    <?php include '../header.php'; ?>

  <!-- Main Content -->
  <main class="ml-60 p-6 flex-1">

    <h1 class="text-3xl font-bold text-gray-800 mb-6">Overview</h1>

    <!-- Cards for quick stats -->
    <!-- <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold">Total Users</h2>
        <p id="user-count" class="text-2xl font-bold text-blue-600">Loading...</p>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold">Total Incidents</h2>
        <p id="incident-count" class="text-2xl font-bold text-green-600">Loading...</p>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold">Assigned Incidents</h2>
        <p id="assigned-count" class="text-2xl font-bold text-yellow-600">Loading...</p>
      </div>
    </div> -->

    <!-- Charts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <!-- User Roles Pie Chart -->
  <div class="bg-white max-w-96 dark:bg-gray-800 p-4 rounded shadow">
    <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">User Roles</h2>
    <canvas id="userChart" height="200"></canvas>
  </div>

  

  <!-- Incident Status Bar Chart -->
  <div class="bg-white max-w-96 dark:bg-gray-800 p-4 rounded shadow">
    <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Incidents by Status</h2>
    <canvas id="incidentChart" height="200"></canvas>
  </div>
</div>
  </main>
  </div>
  
  <script>
fetch('dashboard_data.php')
  .then(res => res.json())
  .then(data => {
    // User Chart
    const userCtx = document.getElementById('userChart').getContext('2d');
    new Chart(userCtx, {
      type: 'pie',
      data: {
        labels: ['Admins', 'IT Staff', 'End Users'],
        datasets: [{
          label: 'User Count',
          data: [
            data.users.admin,
            data.users.staff,
            data.users.user
          ],
          backgroundColor: ['#3b82f6', '#10b981', '#f59e0b']
        }]
      },
      options: { responsive: true }
    });

    // Incident Chart
    const incidentCtx = document.getElementById('incidentChart').getContext('2d');
    new Chart(incidentCtx, {
      type: 'bar',
      data: {
        labels: ['Fixed', 'Pending', 'Not Fixed', 'Assigned'],
        datasets: [{
          label: 'Incidents',
          data: [
            data.incidents.fixed,
            data.incidents.pending,
            data.incidents["not fixed"],
            data.incidents.assigned
          ],
          backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#6366f1']
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1 }
          }
        }
      }
    });
  });
</script>
</body>
</html>
