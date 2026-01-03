<?php
// ===================================
// INDEX.PHP
// Main entry point of the application
// ===================================

session_start();

// Protection: login required
if (!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit;
}

// Main layout
require 'base/header.php';

// Dashboard content
require 'helpers/dashboard.php';

// Footer
require 'base/footer.php';
