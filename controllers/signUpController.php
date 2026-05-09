<?php

session_start();

include "../config/db.php";
include "../models/UserModel.php";

$model = new UserModel($conn);

$message = "";

if (isset($_POST['signup'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // VALIDATION
    if (empty($username) || empty($password) || empty($confirm)) {

        $_SESSION['message'] = "All fields are required!";
        header("Location: ../views/signUp.php");
        exit;
    }

    if ($password !== $confirm) {

        $_SESSION['message'] = "Passwords do not match!";
        header("Location: ../views/signUp.php");
        exit;
    }

    // CHECK EXISTING USER
    $check = $model->userExists($username);

    if ($check->num_rows > 0) {

        $_SESSION['message'] = "Username already exists!";
        header("Location: ../views/signUp.php");
        exit;
    }

    // CREATE USER (CLIENT ONLY)
    $model->createUser($username, $password);

    $_SESSION['message'] = "Account created successfully! You can now log in.";

    header("Location: ../views/login.php");
    exit;
}

?>