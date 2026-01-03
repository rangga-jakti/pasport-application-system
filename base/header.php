<?php
// Start session if it has not been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current file name for active menu state
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Passport Application System</title>

    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="assets/bootstrap.min.css">

    <!-- CUSTOM -->
    <link rel="stylesheet" href="assets/style.css">
</head>

<body style="background:#f4f6f9;">

    <div class="d-flex">

        <!-- SIDEBAR -->
        <aside class="bg-primary text-white p-3" style="width:260px; min-height:100vh;">
            <h4 class="mb-4 text-center">PASSPORT</h4>

            <ul class="nav flex-column">

                <li class="nav-item mb-2">
                    <a href="index.php"
                        class="nav-link text-white <?= $current == 'index.php' ? 'active' : '' ?>">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="application.php"
                        class="nav-link text-white <?= $current == 'application.php' ? 'active' : '' ?>">
                        Application
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="re_registration.php"
                        class="nav-link text-white <?= $current == 're_registration.php' ? 'active' : '' ?>">
                        Re-Registration
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="Processing.php"
                        class="nav-link text-white <?= $current == 'Processing.php' ? 'active' : '' ?>">
                        Processing
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="reports.php"
                        class="nav-link text-white <?= $current == 'reports.php' ? 'active' : '' ?>">
                        Reports
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="rejected_documents.php"
                        class="nav-link text-white <?= $current == 'rejected_documents.php' ? 'active' : '' ?>">
                        Rejected Documents
                    </a>
                </li>

                <hr class="text-white">

                <li class="nav-item mb-2">
                    <a href="auth/change_password.php" class="nav-link text-white">
                        Change Password
                    </a>
                </li>

                <li class="nav-item">
                    <a href="auth/logout.php" class="nav-link text-white fw-bold">
                        Logout
                    </a>
                </li>

            </ul>
        </aside>

        <!-- CONTENT -->
        <main class="flex-grow-1 p-4">