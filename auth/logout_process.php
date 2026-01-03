<?php
session_start();

// Clear all session data
$_SESSION = [];
session_destroy();

// Redirect to login page with logout status
header("Location: login.php?logout=success");
exit;
