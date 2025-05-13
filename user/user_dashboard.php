<!-- User Dashboard -->
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100 dark:bg-gray-900">

  <?php include 'includes/user_sidebar.php'; ?>
  <div class="flex-1 ml-64">
    <?php include 'includes/header.php'; ?>
    <main class="p-6 text-gray-800 dark:text-gray-200">
      <h1 class="text-2xl font-bold">Welcome, User</h1>
      <!-- Add your charts and content here -->
    </main>
  </div>

</body>
</html>
