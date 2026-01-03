<?php
session_start();
require '../config/database.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}

$error = '';
$success = '';

// ===============================
// ALERT AFTER LOGOUT
// ===============================
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $success = 'You have successfully logged out. Please log in again.';
}

// ===============================
// LOGIN PROCESS
// ===============================
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Empty field validation
    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } else {

        // Check user in database
        $stmt = $conn->prepare("
            SELECT * FROM users 
            WHERE username = :username 
            LIMIT 1
        ");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Password validation
        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Invalid username or password.';
        } else {
            // Login successful
            $_SESSION['login']    = true;
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = 'admin';

            header("Location: ../index.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Passport System Login</title>
    <link rel="stylesheet" href="../assets/bootstrap.min.css">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
        <div class="card shadow" style="width:360px;">
            <div class="card-header bg-primary text-white text-center">
                <strong>Passport System Login</strong>
            </div>

            <div class="card-body">

                <!-- LOGOUT SUCCESS ALERT -->
                <?php if ($success): ?>
                    <div class="alert alert-success text-center">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <!-- LOGIN ERROR ALERT -->
                <?php if ($error): ?>
                    <div class="alert alert-danger text-center">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input
                            type="text"
                            name="username"
                            class="form-control"
                            required
                            autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            required>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary w-100">
                        Login
                    </button>
                </form>

            </div>
        </div>
    </div>

</body>

</html>