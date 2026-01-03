<?php
session_start();

// If not logged in, redirect to login page
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Logout Confirmation</title>
    <link rel="stylesheet" href="../assets/bootstrap.min.css">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
        <div class="card shadow" style="width:380px;">
            <div class="card-header bg-danger text-white text-center">
                <strong>Confirm Logout</strong>
            </div>

            <div class="card-body text-center">
                <p class="mb-4">
                    Are you sure you want to log out of the system?
                </p>

                <div class="d-flex justify-content-between">
                    <a href="../index.php" class="btn btn-secondary w-50 me-2">
                        Cancel
                    </a>

                    <a href="logout_process.php" class="btn btn-danger w-50">
                        Yes, Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>