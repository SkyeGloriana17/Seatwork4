<?php
session_start();

require_once __DIR__ . '/../models/userModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/signup.php');
    exit();
}

$fullName = trim($_POST['full_name'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$securityQuestion = trim($_POST['security_question'] ?? '');
$securityAnswer = trim($_POST['security_answer'] ?? '');
$adminCode = trim($_POST['admin_code'] ?? '');

const ADMIN_SECRET_CODE = 'ADMIN2024';

if ($fullName === '' || $username === '' || $password === '' || $confirmPassword === '' || $securityQuestion === '' || $securityAnswer === '') {
    $_SESSION['message'] = 'Please fill in all fields.';
    header('Location: ../views/signup.php');
    exit();
}

if ($password !== $confirmPassword) {
    $_SESSION['message'] = 'Passwords do not match.';
    header('Location: ../views/signup.php');
    exit();
}

$userModel = new UserModel();
$userModel->ensureDefaultAdminAccount();
if ($userModel->findByUsername($username)) {
    $_SESSION['message'] = 'That username is already taken.';
    header('Location: ../views/signup.php');
    exit();
}

// Validate admin code if provided
if ($adminCode !== '' && $adminCode !== ADMIN_SECRET_CODE) {
    $_SESSION['message'] = 'Invalid admin code.';
    header('Location: ../views/signup.php');
    exit();
}

// Determine if creating admin or guest account
$isAdmin = ($adminCode === ADMIN_SECRET_CODE);
if ($isAdmin) {
    $success = $userModel->createAdminUser($fullName, $username, $password, $securityQuestion, $securityAnswer);
} else {
    $success = $userModel->createUser($fullName, $username, $password, $securityQuestion, $securityAnswer);
}

if ($success) {
    $user = $userModel->findByUsername($username);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['role'] = $user['role'];
    header('Location: ../index.php');
    exit();
}

$_SESSION['message'] = 'Unable to create the account. Please try again.';
header('Location: ../views/signup.php');
exit();
