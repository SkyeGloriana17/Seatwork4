<?php

session_start();

include "../config/db.php";

if (isset($_POST['reset'])) {

    $username = $_POST['username'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {

        $_SESSION['message'] = "Passwords do not match!";
        header("Location: ../views/forgotPassword.php");
        exit;
    }

    // update password
    $stmt = $conn->prepare(
        "UPDATE users SET password=? WHERE username=?"
    );

    $stmt->bind_param("ss", $new_password, $username);

    if ($stmt->execute() && $stmt->affected_rows > 0) {

        $_SESSION['message'] = "Password updated successfully!";
    } else {

        $_SESSION['message'] = "User not found!";
    }

    header("Location: ../views/forgotPassword.php");
    exit;
}

?>