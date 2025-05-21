<?php
session_start();
require_once 'config/db.php';
// redirect page straight to login page for now
    header('Location: login.php');
    exit;
?>