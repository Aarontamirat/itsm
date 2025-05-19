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
       <p><?php 
    if (basename(__DIR__)) {
      echo "Welcome to the Admin Dashboard!";
    } else {
      echo "Welcome to Another Dashboard!";
    } ?></p>
    </main>
  </div>

</body>
</html>
