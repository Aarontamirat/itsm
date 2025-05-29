<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit();
}


if ($_FILES['profile_picture']['name']) {
  $user_id = $_SESSION['user_id'];
  $file_name = basename($_FILES['profile_picture']['name']);
  $target = '../uploads/' . $file_name;

  // fetch profile picture of the current logged in user
$stmt = $pdo->prepare(
  "SELECT 
    profile_picture
  FROM
    users
  WHERE
    id = ?"
);
$stmt->execute([$user_id]);
$propic = $stmt->fetchColumn();

// If a file is ready to be uploaded and a file exists in the database, set it to null and also delete the file
if (!empty($propic)) {
  // Delete the old file if it exists on the server
  $oldFile = '../uploads/' . $propic;
  if (file_exists($oldFile)) {
    unlink($oldFile);
  }
  // Set the profile_picture field to null in the database
  $stmt = $pdo->prepare("UPDATE users SET profile_picture = NULL WHERE id = ?");
  $stmt->execute([$user_id]);
}

// Check if a file with the same name already exists
if (file_exists($target)) {
  header("Location: profile.php?error=File with the same name already exists. Please rename your file and try again.");
  exit();
}

// shrink the size of the image before uploading it
$image_info = getimagesize($_FILES['profile_picture']['tmp_name']);
if ($image_info === false) {
  header("Location: profile.php?error=Invalid image file.");
  exit();
}

$max_width = 400;
$max_height = 400;

list($width, $height) = $image_info;

$mime = $image_info['mime'];
switch ($mime) {
  case 'image/jpeg':
    $src_image = imagecreatefromjpeg($_FILES['profile_picture']['tmp_name']);
    break;
  case 'image/png':
    $src_image = imagecreatefrompng($_FILES['profile_picture']['tmp_name']);
    break;
  case 'image/gif':
    $src_image = imagecreatefromgif($_FILES['profile_picture']['tmp_name']);
    break;
  default:
    header("Location: profile.php?error=Unsupported image type.");
    exit();
}

$ratio = min($max_width / $width, $max_height / $height, 1);
$new_width = (int)($width * $ratio);
$new_height = (int)($height * $ratio);

$dst_image = imagecreatetruecolor($new_width, $new_height);

// Preserve transparency for PNG and GIF
if ($mime == 'image/png' || $mime == 'image/gif') {
  imagecolortransparent($dst_image, imagecolorallocatealpha($dst_image, 0, 0, 0, 127));
  imagealphablending($dst_image, false);
  imagesavealpha($dst_image, true);
}

imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

// Overwrite the tmp file with the resized image
switch ($mime) {
  case 'image/jpeg':
    imagejpeg($dst_image, $_FILES['profile_picture']['tmp_name'], 85);
    break;
  case 'image/png':
    imagepng($dst_image, $_FILES['profile_picture']['tmp_name'], 6);
    break;
  case 'image/gif':
    imagegif($dst_image, $_FILES['profile_picture']['tmp_name']);
    break;
}

imagedestroy($src_image);
imagedestroy($dst_image);

if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target)) {
  $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
  $stmt->execute([$file_name, $user_id]);
  header("Location: profile.php?success=Profile picture updated");
  exit();
} else {
  header("Location: profile.php?error=Upload failed");
  exit();
}
}
