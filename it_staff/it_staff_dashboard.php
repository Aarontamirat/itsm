<!-- Include sidebar.php with limited links -->
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>IT Staff Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100 text-black">

  <?php include '../includes/sidebar.php'; ?>
  <div class="flex-1 ml-64">
    <?php include '../header.php'; ?>
    <main class="p-6 ">
      <h1 class="text-2xl font-bold">Welcome, IT Staff</h1>
      <!-- Add your charts and content here -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <!-- Card 1: Open Tickets -->
        <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-center hover:scale-105 transition-transform duration-200 border-t-4 border-blue-500">
          <svg class="w-12 h-12 text-blue-500 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 17v-2a4 4 0 0 1 8 0v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
            <rect x="2" y="17" width="20" height="5" rx="2"></rect>
          </svg>
          <div class="text-3xl font-bold">12</div>
          <div class="text-gray-500 mt-2">Open Tickets</div>
        </div>
        <!-- Card 2: Tickets Resolved -->
        <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-center hover:scale-105 transition-transform duration-200 border-t-4 border-green-500">
          <svg class="w-12 h-12 text-green-500 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M5 13l4 4L19 7"></path>
          </svg>
          <div class="text-3xl font-bold">34</div>
          <div class="text-gray-500 mt-2">Tickets Resolved</div>
        </div>
        <!-- Card 3: Pending Approvals -->
        <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-center hover:scale-105 transition-transform duration-200 border-t-4 border-yellow-500">
          <svg class="w-12 h-12 text-yellow-500 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 8v4l3 3"></path>
            <circle cx="12" cy="12" r="10"></circle>
          </svg>
          <div class="text-3xl font-bold">5</div>
          <div class="text-gray-500 mt-2">Pending Approvals</div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10">
        <!-- Pie Chart Card -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M12 2a10 10 0 0 1 10 10h-10z"></path>
            </svg>
            Ticket Status Overview
          </h2>
          <canvas id="pieChart" height="180"></canvas>
        </div>
        <!-- Bar Chart Card -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
            <svg class="w-6 h-6 text-cyan-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <rect x="3" y="12" width="4" height="8"></rect>
              <rect x="9" y="8" width="4" height="12"></rect>
              <rect x="15" y="4" width="4" height="16"></rect>
            </svg>
            Tickets by Category
          </h2>
          <canvas id="barChart" height="180"></canvas>
        </div>
      </div>

      <!-- Recent Activity Table -->
      <div class="bg-white rounded-xl shadow-lg p-6 mt-10">
        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
          <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7"></path>
            <path d="M16 3v4"></path>
            <path d="M8 3v4"></path>
            <rect x="3" y="7" width="18" height="13" rx="2"></rect>
          </svg>
          Recent Activity
        </h2>
        <div class="overflow-x-auto">
          <table class="min-w-full text-left text-sm">
            <thead>
              <tr>
                <th class="py-2 px-4 font-semibold text-gray-700">Date</th>
                <th class="py-2 px-4 font-semibold text-gray-700">Ticket</th>
                <th class="py-2 px-4 font-semibold text-gray-700">Status</th>
                <th class="py-2 px-4 font-semibold text-gray-700">Assigned To</th>
              </tr>
            </thead>
            <tbody>
              <tr class="hover:bg-gray-50">
                <td class="py-2 px-4">2024-06-01</td>
                <td class="py-2 px-4">Printer not working</td>
                <td class="py-2 px-4"><span class="bg-blue-100 text-blue-700 px-2 py-1 rounded">Open</span></td>
                <td class="py-2 px-4">John Doe</td>
              </tr>
              <tr class="hover:bg-gray-50">
                <td class="py-2 px-4">2024-05-30</td>
                <td class="py-2 px-4">Email issue</td>
                <td class="py-2 px-4"><span class="bg-green-100 text-green-700 px-2 py-1 rounded">Resolved</span></td>
                <td class="py-2 px-4">Jane Smith</td>
              </tr>
              <tr class="hover:bg-gray-50">
                <td class="py-2 px-4">2024-05-29</td>
                <td class="py-2 px-4">Network downtime</td>
                <td class="py-2 px-4"><span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded">Pending</span></td>
                <td class="py-2 px-4">Alex Lee</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Chart.js CDN -->
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script>
        // Pie Chart
        new Chart(document.getElementById('pieChart'), {
          type: 'doughnut',
          data: {
            labels: ['Open', 'Resolved', 'Pending'],
            datasets: [{
              data: [12, 34, 5],
              backgroundColor: ['#3b82f6', '#22c55e', '#facc15'],
              borderWidth: 2
            }]
          },
          options: {
            plugins: {
              legend: { display: true, position: 'bottom' }
            }
          }
        });

        // Bar Chart
        new Chart(document.getElementById('barChart'), {
          type: 'bar',
          data: {
            labels: ['Hardware', 'Software', 'Network', 'Other'],
            datasets: [{
              label: 'Tickets',
              data: [8, 15, 6, 7],
              backgroundColor: ['#6366f1', '#06b6d4', '#f59e42', '#a78bfa']
            }]
          },
          options: {
            plugins: {
              legend: { display: false }
            },
            scales: {
              y: { beginAtZero: true }
            }
          }
        });
      </script>
    </main>
  </div>

</body>
</html>
