<?php
session_start();

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>

    <link rel="stylesheet" href="../assets/style4.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;600;800&display=swap');
    </style>
</head>

<body>

<nav>
    <div class="headerTitle">
        Comfort Zone Hotel
    </div>

    <a href="index.php">HOME</a>
    <a href="profile.php">COMPANY PROFILE</a>
    <a href="reservation.php">RESERVATION</a>
    <a href="contact.php">CONTACTS</a>
    <a href="admin.php">ADMIN</a>
</nav>

<div class="container">

    <h1>Forgot Password</h1>

    <p>Enter your username and new password.</p>

    <form action="../controllers/ForgotPasswordController.php" method="POST">

        <label for="username">Username:</label>
        <input 
            type="text" 
            id="username" 
            name="username" 
            required
        >

        <br><br>

        <label for="new_password">New Password:</label>

        <div style="position: relative;">
            <input 
                type="password" 
                id="new_password"
                name="new_password" 
                required
            >

            <button 
                type="button"
                onclick="togglePassword('new_password')"
                style="position:absolute; right:-10px; top:0px;"
            >
                👁
            </button>
        </div>

        <br><br>

        <label for="confirm_password">Confirm Password:</label>

        <div style="position: relative;">
            <input 
                type="password" 
                id="confirm_password"
                name="confirm_password" 
                required
            >

            <button 
                type="button"
                onclick="togglePassword('confirm_password')"
                style="position:absolute; right:-10px; top:0px;"
            >
                👁
            </button>
        </div>

        <br><br>

        <button type="submit" name="reset">
            Reset Password
        </button>

    </form>

    <div class="message">
        <?= htmlspecialchars($message) ?>
    </div>

    <br>

    <a href="login.php">← Back to Login</a>

</div>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);

    input.type = input.type === "password"
        ? "text"
        : "password";
}
</script>

</body>
</html>