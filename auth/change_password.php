<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Account</title>
    <link rel="stylesheet" href="../assets/bootstrap.min.css">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
        <div class="card shadow" style="width:400px;">
            <div class="card-header bg-warning text-dark text-center">
                <strong>Update Account</strong>
            </div>

            <div class="card-body">
                <form method="POST"
                    action="change_password_process.php"
                    onsubmit="return confirm('Are you sure you want to save these changes? You will be logged out after this.')">

                    <!-- NEW USERNAME -->
                    <div class="mb-3">
                        <label class="form-label">New Username</label>
                        <input
                            type="text"
                            name="username_baru"
                            class="form-control"
                            placeholder="Leave empty if unchanged">
                        <small class="text-muted">
                            Username only needs to be entered once (no confirmation required).
                        </small>
                    </div>

                    <hr>

                    <!-- NEW PASSWORD -->
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input
                            type="password"
                            name="password_baru"
                            class="form-control"
                            placeholder="Leave empty if unchanged">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input
                            type="password"
                            name="konfirmasi_password"
                            class="form-control"
                            placeholder="Re-enter new password">
                    </div>

                    <button type="submit" class="btn btn-warning w-100">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>