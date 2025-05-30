<?php
session_start();
require_once '../config/db.php';

$directoryName = basename(__DIR__);

// check if the user is logged in and has the right role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

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
  <main class="flex justify-center items-start min-h-[80vh] py-10 px-2 bg-transparent">
    <div class="bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 pt-14 fade-in tech-border glow max-w-4xl w-full">
      <h1 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Admin Dashboard</h1>
      <p class="text-center text-cyan-500 mb-1 font-mono">Overview of system users and incidents</p>
      <p class="text-center text-green-500 mb-6 font-mono text-xs">Property of Lucy Insurance</p>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- User Roles Pie Chart -->
        <div class="bg-cyan-50 bg-opacity-80 rounded-xl p-6 shadow-lg border border-cyan-100 flex flex-col items-center">
          <h2 class="text-lg font-semibold mb-4 text-cyan-700 font-mono">User Roles</h2>
          <canvas id="userChart" height="200"></canvas>
        </div>
        <!-- Incident Status Bar Chart -->
        <div class="bg-cyan-50 bg-opacity-80 rounded-xl p-6 shadow-lg border border-cyan-100 flex flex-col items-center">
          <h2 class="text-lg font-semibold mb-4 text-cyan-700 font-mono">Incidents by Status</h2>
          <canvas id="incidentChart" height="200"></canvas>
        </div>
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
