<?php
session_start();

require_once __DIR__ . '/../models/userModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/reset_password.php');
    exit();
}

if (!isset($_SESSION['reset_user_id']) || !isset($_SESSION['reset_username'])) {
    $_SESSION['message'] = 'Session expired. Please try again.';
    header('Location: ../views/forgot_password.php');
    exit();
}

$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if ($new_password === '' || $confirm_password === '') {
    $_SESSION['message'] = 'Please enter both password fields.';
    header('Location: ../views/reset_password.php');
    exit();
}

if ($new_password !== $confirm_password) {
    $_SESSION['message'] = 'Passwords do not match.';
    header('Location: ../views/reset_password.php');
    exit();
}

if (strlen($new_password) < 6) {
    $_SESSION['message'] = 'Password must be at least 6 characters long.';
    header('Location: ../views/reset_password.php');
    exit();
}

$userModel = new UserModel();
if ($userModel->updatePassword($_SESSION['reset_user_id'], $new_password)) {
    unset($_SESSION['reset_user_id']);
    unset($_SESSION['reset_username']);
    $_SESSION['message'] = 'Password reset successfully! Please login with your new password.';
    header('Location: ../views/login.php');
    exit();
} else {
    $_SESSION['message'] = 'Error resetting password. Please try again.';
    header('Location: ../views/reset_password.php');
    exit();
}
