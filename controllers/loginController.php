<?php

session_start();

include "../config/db.php";
include "../models/loginModel.php";

$model = new LoginModel($conn);

// LOGIN LOGIC
if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // VALIDATION
    if (empty($username) || empty($password)) {

        $_SESSION['message'] = "Please fill in all fields.";

        header("Location: ../views/login.php");
        exit;
    }

    // CHECK USER
    $result = $model->loginUser($username, $password);

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        // STORE SESSION
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // REDIRECT
        if ($user['role'] === 'admin') {

            header("Location: ../views/admin.php");
        }

        else {

            header("Location: ../views/guest.php");
        }

        exit();
    }

    else {

        $_SESSION['message'] = "Invalid username or password!";

        header("Location: ../views/login.php");
        exit();
    }
}

?>