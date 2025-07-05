<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit();
}

require_once '../config/db.php'; // your existing PDO db connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['backup'])) {
  try {
    $tablesStmt = $pdo->query("SHOW TABLES");
    $tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($tables)) throw new Exception("No tables found!");

    $backupContent = "-- Database Backup\n";
    $backupContent .= "-- Generated: " . date("Y-m-d H:i:s") . "\n\n";

    foreach ($tables as $table) {
      // Add table creation statement
      $createStmt = $pdo->query("SHOW CREATE TABLE `$table`");
      $createResult = $createStmt->fetch(PDO::FETCH_ASSOC);
      $backupContent .= "-- ---------------------------\n";
      $backupContent .= "-- Table structure for `$table`\n";
      $backupContent .= "-- ---------------------------\n";
      $backupContent .= $createResult['Create Table'] . ";\n\n";

      // Add table data
      $rowsStmt = $pdo->query("SELECT * FROM `$table`");
      $rows = $rowsStmt->fetchAll(PDO::FETCH_ASSOC);
      if (!empty($rows)) {
        $backupContent .= "-- ---------------------------\n";
        $backupContent .= "-- Data for table `$table`\n";
        $backupContent .= "-- ---------------------------\n";

        foreach ($rows as $row) {
          $cols = array_map(fn($col) => "`" . str_replace("`", "``", $col) . "`", array_keys($row));
          $vals = array_map(fn($val) => $val === null ? "NULL" : $pdo->quote($val), array_values($row));
          $backupContent .= "INSERT INTO `$table` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ");\n";
        }
        $backupContent .= "\n";
      }
    }

    $backupFile = 'itsm_backup_' . date("Y-m-d_H-i-s") . '.sql';

    // Send file for download
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $backupFile . '"');
    header('Content-Length: ' . strlen($backupContent));
    echo $backupContent;
    exit();

  } catch (Exception $e) {
    $error = "Backup failed: " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Database Backup</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

  <!-- header and sidebar -->
  <?php include '../includes/sidebar.php'; ?>
  <?php include '../header.php'; ?>

  <div class="max-w-4xl ms-auto bg-white bg-opacity-95 rounded-2xl shadow-2xl px-8 py-10 fade-in tech-border glow mt-8">
    <h2 class="text-3xl font-extrabold text-center text-cyan-700 mb-2 tracking-tight font-mono">Database Backup</h2>
    <p class="text-center text-cyan-500 mb-6 font-mono">Click the button below to download a backup of the entire database.</p>

    <!-- Success/Error Messages -->
    <?php if (!empty($error)): ?>
      <div id="error-message" class="mb-4 text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
        <?php echo htmlspecialchars($error); ?>
      </div>
      <script>
        setTimeout(() => document.getElementById('error-message').style.opacity = '1', 10);
        setTimeout(() => document.getElementById('error-message').style.opacity = '0', 3010);
      </script>
    <?php elseif (!empty($success)): ?>
      <div id="success-message" class="mb-4 text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-center font-mono font-semibold opacity-0 transition-opacity duration-500">
        <?php echo htmlspecialchars($success); ?>
      </div>
      <script>
        setTimeout(() => document.getElementById('success-message').style.opacity = '1', 10);
        setTimeout(() => document.getElementById('success-message').style.opacity = '0', 3010);
      </script>
    <?php endif; ?>

    <div class="flex justify-center">
      <form method="POST">
        <button type="submit" name="backup"
          class="px-8 py-4 bg-gradient-to-r from-cyan-400 via-cyan-300 to-green-300 hover:from-green-300 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg transform hover:scale-105 transition duration-300 font-mono tracking-widest text-lg">
          📦 Backup Now
        </button>
      </form>
    </div>
  </div>

</body>

</html>

