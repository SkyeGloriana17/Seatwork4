<?php
session_start();

require_once __DIR__ . '/../models/userModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/login.php');
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['message'] = 'Please enter both username and password.';
    header('Location: ../views/login.php');
    exit();
}

$userModel = new UserModel();
$userModel->ensureDefaultAdminAccount();
$user = $userModel->authenticate($username, $password);

if (!$user) {
    $_SESSION['message'] = 'Invalid username or password!';
    header('Location: ../views/login.php');
    exit();
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['full_name'] = $user['full_name'] ?? $user['username'];
$_SESSION['role'] = $user['role'];

if ($user['role'] === 'admin') {
    header('Location: ../views/admin.php');
} else {
    header('Location: ../index.php');
}
exit();
