<?php
session_start();
require '../config/database.php';

// Ensure user is logged in
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$new_username   = trim($_POST['username_baru'] ?? '');
$new_password   = trim($_POST['password_baru'] ?? '');
$confirm_pass   = trim($_POST['konfirmasi_password'] ?? '');

/* ===============================
   PASSWORD VALIDATION
================================ */
if ($new_password !== '') {
    if ($new_password !== $confirm_pass) {
        die('Password confirmation does not match.');
    }
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
}

/* ===============================
   BUILD DYNAMIC QUERY
================================ */
$fields = [];
$params = ['id' => $user_id];

if ($new_username !== '') {
    $fields[] = 'username = :username';
    $params['username'] = $new_username;
}

if ($new_password !== '') {
    $fields[] = 'password = :password';
    $params['password'] = $password_hash;
}

if (empty($fields)) {
    die('No changes were saved.');
}

/* ===============================
   UPDATE DATABASE
================================ */
$sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute($params);

/* ===============================
   FORCE LOGOUT
================================ */
session_destroy();
header("Location: change_password_success.php");
exit;
