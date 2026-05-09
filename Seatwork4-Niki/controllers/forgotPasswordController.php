<?php
session_start();

require_once __DIR__ . '/../models/userModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/forgot_password.php');
    exit();
}

$username = trim($_POST['username'] ?? '');
$securityQuestion = trim($_POST['security_question'] ?? '');
$securityAnswer = trim($_POST['security_answer'] ?? '');

if ($username === '' || $securityQuestion === '' || $securityAnswer === '') {
    $_SESSION['message'] = 'Please complete all reset verification fields.';
    header('Location: ../views/forgot_password.php');
    exit();
}

$userModel = new UserModel();
$user = $userModel->verifySecurityAnswer($username, $securityQuestion, $securityAnswer);

if (!$user) {
    $_SESSION['message'] = 'Reset verification failed.';
    header('Location: ../views/forgot_password.php');
    exit();
}

$_SESSION['reset_username'] = $username;
$_SESSION['reset_user_id'] = $user['id'];

header('Location: ../views/reset_password.php');
exit();
